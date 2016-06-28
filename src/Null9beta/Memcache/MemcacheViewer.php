<?php

namespace Null9beta\Memcache;

use Null9beta\Memcache\Config\MemcacheConfigConstants;

class MemcacheViewer
{

    /**
     * @var \Memcached
     */
    protected $memcache;

    /**
     * MemcacheViewer constructor.
     * @param \Memcached $memcache
     */
    public function __construct(\Memcached $memcache)
    {
        $this->memcache = $memcache;
    }

    /**
     * @return \Memcached
     */
    public function getMemcache()
    {
        return $this->memcache;
    }

    /**
     * @param $key
     * @return bool
     */
    public function memcacheHasKey($key)
    {
        $this->memcache->get($key);

        return $this->memcache->getResultCode() !== MemcacheReturnCodes::MEMCACHED_NOTFOUND;
    }

    /**
     * @param string $filter
     * @param bool $asMap
     * @return array
     */
    public function find($filter = '', $asMap = true)
    {
        $filterPattern = "/{$filter}/";

        $keys = $this->getMemcacheKeys();

        $items = [];
        foreach ($keys as $key) {

            if (preg_match($filterPattern, $key) == false) {
                continue;
            }

            $value = $this->memcache->get($key);

            if ($asMap) {
                $items[$key] = $value;
            } else {
                $items[] = [
                    $key,
                    $value,
                ];
            }
        }

        return $items;
    }

    /**
     * @return array
     */
    protected function getMemcacheKeys()
    {
        $keys = [];
        foreach ($this->memcache->getServerList() as $server) {
            $keys = array_merge(
                $keys,
                $this->getMemcacheKeysForHost(
                    $server[MemcacheConfigConstants::SERVER_HOST],
                    $server[MemcacheConfigConstants::SERVER_PORT]
                )
            );
        }

        return $keys;
    }

    /**
     * @link function taken from http://stackoverflow.com/a/34724821
     *
     * @param string $host
     * @param int $port
     * @return array|int
     */
    protected static function getMemcacheKeysForHost($host = '127.0.0.1', $port = 11211)
    {
        $mem = @fsockopen($host, $port);
        if ($mem === false) {
            return -1;
        }

        // retrieve distinct slab
        $r = @fwrite($mem, 'stats items' . chr(10));
        if (false === $r) {
            return -2;
        }

        $slab = [];
        while (($l = @fgets($mem, 1024)) !== false) {
            // sortie ?
            $l = trim($l);
            if ($l == 'END') {
                break;
            }

            $m = [];
            // <STAT items:22:evicted_nonzero 0>
            $r = preg_match('/^STAT\sitems\:(\d+)\:/', $l, $m);
            if ($r != 1) {
                return -3;
            }
            $a_slab = $m[1];

            if (!array_key_exists($a_slab, $slab)) {
                $slab[$a_slab] = [];
            }
        }

        // recuperer les items
        reset($slab);
        foreach ($slab as $a_slab_key => &$a_slab) {
            $r = @fwrite($mem, 'stats cachedump ' . $a_slab_key . ' 100' . chr(10));
            if (false === $r) {
                return -4;
            }

            while (($l = @fgets($mem, 1024)) !== false) {
                // sortie ?
                $l = trim($l);
                if ($l == 'END') {
                    break;
                }

                $m = [];
                // ITEM 42 [118 b; 1354717302 s]
                $r = preg_match('/^ITEM\s([^\s]+)\s/', $l, $m);
                if ($r != 1) {
                    return -5;
                }
                $a_key = $m[1];
                $a_slab[] = $a_key;
            }
        }

        // close
        @fclose($mem);
        unset($mem);

        // transform it;
        $keys = [];
        reset($slab);
        foreach ($slab as &$a_slab) {
            reset($a_slab);
            foreach ($a_slab as &$a_key) {
                $keys[] = $a_key;
            }
        }
        unset($slab);

        natsort($keys);

        return $keys;
    }
}

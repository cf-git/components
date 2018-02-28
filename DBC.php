<?php
/**
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * Copyright (c) 2017, Ukraine, Shubin Sergei
 *
 * Database Connector trait
 * @package      : cf-git/tools
 * @author       : is.captain.fail@gmail.com
 * @user         : CF
 * @license      : http://opensource.org/licenses/MIT   MIT License
 */
namespace CF\Components;

use CF\Log\Logger;
use Psr\Log\LoggerInterface;

trait DBC
{
    use DBT;
    /** @var null|\PDO $connection */
    private $connection = null;

    /**
     * @return mixed|Logger|LoggerInterface
     */
    abstract protected static function log();

    private $host = null;
    private $port = null;
    private $user = null;
    private $pass = null;
    private $dbName = null;
    private $charset = null;
    private $dbProfiling = null;

    /**
     * @param string $host
     * @param string $port
     * @param string $user
     * @param string $pass
     * @param string $dbName
     * @param string $charset
     * @param int $dbProfiling
     */
    protected function addDBConnectionSetting($host, $port, $user, $pass, $dbName, $charset, $dbProfiling)
    {
        $this->host = $host;
        $this->port = $port;
        $this->user = $user;
        $this->pass = $pass;
        $this->dbName = $dbName;
        $this->charset = $charset;
        $this->dbProfiling = $dbProfiling;
    }

    /**
     * Receives constant params
     * @return \PDO connection
     */
    public function db()
    {
        if (is_null($this->connection)) {
            try {
                $dns = "mysql:host={$this->host};port={$this->port};dbname={$this->dbName};charset={$this->charset};";
                $opts = [
                    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
                ];
                $this->connection = new \PDO($dns, $this->user, $this->pass, $opts);
                $this->connection->query("SET profiling={$this->dbProfiling}");

            } catch (\Throwable $e) {
                static::log()->error($e->getMessage(), (array)$e);
                die($e);
            }
        }
        return $this->connection;
    }

    /**
     * Query sender
     * @param $string
     * @param bool $isPrepare
     * @return \PDOStatement
     */
    public function query($string, $isPrepare = false):\PDOStatement
    {
        return $isPrepare ? $this->db()->prepare($string) : $this->db()->query($string);
    }
}
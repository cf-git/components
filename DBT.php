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
 * Database Connector trait tools
 * @package      : cf-git/tools
 * @author       : is.captain.fail@gmail.com
 * @user         : CF
 * @license      : http://opensource.org/licenses/MIT   MIT License
 */
namespace CF\Components;

use CF\Log\Logger;
use Psr\Log\LoggerInterface;

trait DBT
{

    /**
     * @return mixed|\PDO
     */
    abstract protected function db();

    /**
     * @return mixed|Logger|LoggerInterface
     */
    abstract protected static function log();
    /****************************************/
    private $rollbacks = false;

    /****************************************/
    /**
     * @return string|int|null
     */
    public function lastInsertId()
    {
        return $this->db()->lastInsertId();
    }

    /**
     *
     */
    public function beginTransaction()
    {
        $this->rollbacks = false;
        $this->db()->beginTransaction();
        return $this->db();
    }

    /**
     *
     */
    public function rollback()
    {
        try {
            $this->rollbacks = true;
            $this->db()->rollBack();
        } catch (\Throwable $e) {
            self::log()->error($e->getMessage(), $e);
        }
    }

    public function commit()
    {
        try {
            $this->db()->commit();
        } catch (\Throwable $e) {
            self::log()->error($e->getMessage(), $e);
        }
    }
    /****************************************/
    /**
     * @return bool
     */
    public function isHasRollbacks()
    {
        return $this->rollbacks;
    }
    /****************************************/
}
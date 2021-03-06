<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 encoding=utf-8: */
// +----------------------------------------------------------------------+
// | Eventum - Issue Tracking System                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2015 Eventum Team.                                     |
// |                                                                      |
// | This program is free software; you can redistribute it and/or modify |
// | it under the terms of the GNU General Public License as published by |
// | the Free Software Foundation; either version 2 of the License, or    |
// | (at your option) any later version.                                  |
// |                                                                      |
// | This program is distributed in the hope that it will be useful,      |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of       |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        |
// | GNU General Public License for more details.                         |
// |                                                                      |
// | You should have received a copy of the GNU General Public License    |
// | along with this program; if not, write to:                           |
// |                                                                      |
// | Free Software Foundation, Inc.                                       |
// | 51 Franklin Street, Suite 330                                        |
// | Boston, MA 02110-1301, USA.                                          |
// +----------------------------------------------------------------------+
// | Authors: Elan Ruusamäe <glen@delfi.ee>                               |
// +----------------------------------------------------------------------+

/**
 * Class dealing with user passwords
 */
class AuthPassword
{
    /**
     * Hash the password
     *
     * @param string $password The password to hash
     * @return string The hashed password, throws on error.
     * @throws RuntimeException
     */
    public static function hash($password)
    {
        $res = password_hash($password, PASSWORD_DEFAULT);
        if (!$res) {
            throw new RuntimeException("password hashing failed");
        }
        return $res;
    }

    /**
     * Verify a password against a hash using a timing attack resistant approach
     *
     * @param string $hash The hash to verify against
     * @param string $password The password to verify
     * @return boolean If the password matches the hash
     * @throws InvalidArgumentException in case non-strings were passed as hash or password
     */
    public static function verify($password, $hash)
    {
        if (!is_string($password) || !is_string($hash)) {
            throw new InvalidArgumentException("password and hash need to be strings");
        }

        // verify passwords in constant time, i.e always do all checks
        $cmp = 0;

        $cmp |= (int)password_verify($password, $hash);

        // legacy authentication methods
        $cmp |= (int)self::cmp($hash, base64_encode(pack('H*', md5($password))));
        $cmp |= (int)self::cmp($hash, md5($password));

        return (bool)$cmp;
    }

    /**
     * Compare strings using a timing attack resistant approach
     *
     * @param string $str1
     * @param string $str2
     * @return bool
     */
    private static function cmp($str1, $str2)
    {
        if (Misc::countBytes($str1) != Misc::countBytes($str2) || Misc::countBytes($str1) <= 13) {
            return false;
        }

        $status = 0;
        $length = Misc::countBytes($str1);
        for ($i = 0; $i < $length; $i++) {
            $status |= (ord($str1[$i]) ^ ord($str2[$i]));
        }

        return $status === 0;
    }
}

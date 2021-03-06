<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 encoding=utf-8: */
// +----------------------------------------------------------------------+
// | Eventum - Issue Tracking System                                      |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 - 2008 MySQL AB                                   |
// | Copyright (c) 2008 - 2010 Sun Microsystem Inc.                       |
// | Copyright (c) 2011 - 2015 Eventum Team.                              |
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
// | Authors: João Prado Maia <jpm@mysql.com>                             |
// +----------------------------------------------------------------------+

require_once __DIR__ . '/../init.php';

Auth::checkAuthentication();

if (@$_GET['cat'] == 'blocked_email') {
    $email = Note::getBlockedMessage($_GET['note_id']);
} else {
    $email = Support::getFullEmail($_GET['sup_id']);
}
if (!empty($_GET['raw'])) {
    Attachment::outputDownload($email, 'message.eml', Misc::countBytes($email), 'message/rfc822');
} else {
    if (!empty($_GET['cid'])) {
        list($mimetype, $data) = Mime_Helper::getAttachment($email, $_GET['filename'], $_GET['cid']);
    } else {
        list($mimetype, $data) = Mime_Helper::getAttachment($email, $_GET['filename']);
    }
    Attachment::outputDownload($data, $_GET['filename'], Misc::countBytes($data), $mimetype);
}

<?php
/*
 * @Created by: VSCode
 * @User: BigLop
 * @Date: 2020-06-26 09:15:35
 * @LastEditTime: 2020-06-27 11:33:18
 * @FilePath: \dev\application\common.php
 */

// 应用公共文件

/**
 * 按json方式输出通信数据
 * @param array $data 数据
 * @param integer $code 状态码
 * @param string $msg 提示信息
 * @param string $headerCode 状态码
 * @return string
 */
function json_return($data = [], $code = 1, $msg = '', $other = [])
{
    return json(array_merge(['code' => $code, 'data' => $data, 'msg' => $msg], $other));
}

/**
 * 获取模块URL路径
 */
function module_url()
{
    global $_W;
    return $_W['siteroot'] . "addons/" . $_W['current_module']['name'] . "/";
}

/**
 * 压缩 HTML 代码
 *
 * @param string $html_source HTML 源码
 * @return string 压缩后的代码
 */
function compressHtml($html_source)
{
    $chunks   = preg_split('/(<!--<nocompress>-->.*?<!--<\/nocompress>-->|<nocompress>.*?<\/nocompress>|<pre.*?\/pre>|<textarea.*?\/textarea>|<script.*?\/script>)/msi', $html_source, -1, PREG_SPLIT_DELIM_CAPTURE);
    $compress = '';
    foreach ($chunks as $c) {
        if (strtolower(substr($c, 0, 19)) == '<!--<nocompress>-->') {
            $c        = substr($c, 19, strlen($c) - 19 - 20);
            $compress .= $c;
            continue;
        } elseif (strtolower(substr($c, 0, 12)) == '<nocompress>') {
            $c        = substr($c, 12, strlen($c) - 12 - 13);
            $compress .= $c;
            continue;
        } elseif (strtolower(substr($c, 0, 4)) == '<pre' || strtolower(substr($c, 0, 9)) == '<textarea') {
            $compress .= $c;
            continue;
        } elseif (strtolower(substr($c, 0, 7)) == '<script' && strpos($c, '//') != false && (strpos($c, "\r") !== false || strpos($c, "\n") !== false)) { // JS代码，包含“//”注释的，单行代码不处理
            $tmps = preg_split('/(\r|\n)/ms', $c, -1, PREG_SPLIT_NO_EMPTY);
            $c    = '';
            foreach ($tmps as $tmp) {
                if (strpos($tmp, '//') !== false) { // 对含有“//”的行做处理
                    if (substr(trim($tmp), 0, 2) == '//') { // 开头是“//”的就是注释
                        continue;
                    }
                    $chars   = preg_split('//', $tmp, -1, PREG_SPLIT_NO_EMPTY);
                    $is_quot = $is_apos = false;
                    foreach ($chars as $key => $char) {
                        if ($char == '"' && !$is_apos && $key > 0 && $chars[$key - 1] != '\\') {
                            $is_quot = !$is_quot;
                        } elseif ($char == '\'' && !$is_quot && $key > 0 && $chars[$key - 1] != '\\') {
                            $is_apos = !$is_apos;
                        } elseif ($char == '/' && $chars[$key + 1] == '/' && !$is_quot && !$is_apos) {
                            $tmp = substr($tmp, 0, $key); // 不是字符串内的就是注释
                            break;
                        }
                    }
                }
                $c .= $tmp;
            }
        }

        $c        = preg_replace('/[\\n\\r\\t]+/', ' ', $c); // 清除换行符，清除制表符
        $c        = preg_replace('/\\s{2,}/', ' ', $c); // 清除额外的空格
        $c        = preg_replace('/>\\s</', '> <', $c); // 清除标签间的空格
        $c        = preg_replace('/\\/\\*.*?\\*\\//i', '', $c); // 清除 CSS & JS 的注释
        $c        = preg_replace('/<!--[^!]*-->/', '', $c); // 清除 HTML 的注释
        $compress .= $c;
    }
    return $compress;
}

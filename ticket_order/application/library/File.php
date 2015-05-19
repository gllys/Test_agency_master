<?php
class File{
    /**
     * 下载文件保存到指定位置
     *
     * @param $url
     * @param $filepath
     *
     * @return bool
     */
    public static function saveFile($url, $filepath) {
        if (Validate::isAbsoluteUrl($url) && !empty($filepath)) {
            $file = self::file_get_contents($url);
            $fp = @fopen($filepath, 'w');
            if ($fp) {
                @fwrite($fp, $file);
                @fclose($fp);

                return $filepath;
            }
        }

        return false;
    }

    /**
     * 文件复制
     *
     * @param $source
     * @param $dest
     *
     * @return bool
     */
    public static function copyFile($source, $dest) {
        if (file_exists($dest) || is_dir($dest)) {
            return false;
        }

        return copy($source, $dest);
    }

    public static function sys_get_temp_dir() {
        if (function_exists('sys_get_temp_dir')) {
            return sys_get_temp_dir();
        }
        if ($temp = getenv('TMP')) {
            return $temp;
        }
        if ($temp = getenv('TEMP')) {
            return $temp;
        }
        if ($temp = getenv('TMPDIR')) {
            return $temp;
        }
        $temp = tempnam(__FILE__, '');
        if (file_exists($temp)) {
            unlink($temp);

            return dirname($temp);
        }

        return null;
    }

    /**
     * 遍历路径
     *
     * @param        $path
     * @param string $ext
     * @param string $dir
     * @param bool   $recursive
     *
     * @return array
     */
    public static function scandir($path, $ext = 'php', $dir = '', $recursive = false) {
        $path = rtrim(rtrim($path, '\\'), '/') . '/';
        $real_path = rtrim(rtrim($path . $dir, '\\'), '/') . '/';
        $files = scandir($real_path);
        if (!$files)
            return array();

        $filtered_files = array();

        $real_ext = false;
        if (!empty($ext))
            $real_ext = '.' . $ext;
        $real_ext_length = strlen($real_ext);

        $subdir = ($dir) ? $dir . '/' : '';
        foreach ($files as $file) {
            if (!$real_ext || (strpos($file, $real_ext) && strpos($file, $real_ext) == (strlen($file) - $real_ext_length)))
                $filtered_files[] = $subdir . $file;

            if ($recursive && $file[0] != '.' && is_dir($real_path . $file))
                foreach (File::scandir($path, $ext, $subdir . $file, $recursive) as $subfile)
                    $filtered_files[] = $subfile;
        }

        return $filtered_files;
    }

    /**
     * 获取文件扩展名
     *
     * @param $file
     *
     * @return mixed|string
     */
    public static function getFileExtension($file) {
        if (is_uploaded_file($file)) {
            return "unknown";
        }

        return pathinfo($file, PATHINFO_EXTENSION);
    }

    public static function ZipTest($from_file) {
        $zip = new PclZip($from_file);

        return ($zip->privCheckFormat() === true);
        /*
          if (class_exists('ZipArchive', false)) {
          $zip = new ZipArchive();
          return ($zip->open($from_file, ZIPARCHIVE::CHECKCONS) === true);
          }
          else {
          $zip = new PclZip($from_file);
          return ($zip->privCheckFormat() === true);
          }
         */
    }

    public static function ZipExtract($from_file, $to_dir) {
        if (!file_exists($to_dir))
            mkdir($to_dir, 0777);
        $zip = new PclZip($from_file);
        $list = $zip->extract(PCLZIP_OPT_PATH, $to_dir);

        return $list;
        /*
          if (class_exists('ZipArchive', false)) {
          $zip = new ZipArchive();
          if ($zip->open($from_file) === true && $zip->extractTo($to_dir) && $zip->close())
          return true;
          return false;
          }
          else {
          $zip = new PclZip($from_file);
          $list = $zip->extract(PCLZIP_OPT_PATH, $to_dir);
          foreach ($list as $file)
          if ($file['status'] != 'ok' && $file['status'] != 'already_a_directory')
          return false;
          return true;
          }
         */
    }

    /**
     * 根据时间生成图片名
     *
     * @param string $image_type
     *
     * @return float|string
     */
    public static function getTimeImageName($image_type = "image/jpeg") {
        if ($image_type == "image/jpeg" || $image_type == "image/pjpeg") {
            return self::getmicrotime() . ".jpg";
        } elseif ($image_type == "image/gif") {
            return self::getmicrotime() . ".gif";
        } elseif ($image_type == "image/png") {
            return self::getmicrotime() . ".png";
        } else {
            return self::getmicrotime();
        }
    }

    /**
     * 获取服务器配置允许最大上传文件大小
     *
     * @param int $max_size
     *
     * @return mixed
     */
    public static function getMaxUploadSize($max_size = 0) {
        $post_max_size = Tools::convertBytes(ini_get('post_max_size'));
        $upload_max_filesize = Tools::convertBytes(ini_get('upload_max_filesize'));
        if ($max_size > 0)
            $result = min($post_max_size, $upload_max_filesize, $max_size);
        else
            $result = min($post_max_size, $upload_max_filesize);

        return $result;
    }

    /**
     * 删除文件夹
     *
     * @param      $dirname
     * @param bool $delete_self
     */
    public static function deleteDirectory($dirname, $delete_self = true) {
        $dirname = rtrim($dirname, '/') . '/';
        if (is_dir($dirname)) {
            $files = scandir($dirname);
            foreach ($files as $file)
                if ($file != '.' && $file != '..' && $file != '.svn') {
                    if (is_dir($dirname . $file))
                        File::deleteDirectory($dirname . $file, true);
                    elseif (file_exists($dirname . $file))
                        unlink($dirname . $file);
                }
            if ($delete_self)
                rmdir($dirname);
        }
    }
}
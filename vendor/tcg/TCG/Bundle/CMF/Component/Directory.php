<?php

namespace TCG\Bundle\CMF\Component;


class Directory
{

    /**
     * 计算相对路径
     * @param $targetPath
     * @param $basePath
     * @return string
     */
    public function relativePath($targetPath, $basePath)
    {
        $targetPathArr = explode('/', $targetPath);
        $basePathArr = explode('/', $basePath);

        $targetDiff2Base = array_diff_assoc($targetPathArr, $basePathArr);

        $previousCount = 0;
        if ($targetDiff2Base) {
            $previousCount = array_keys($targetDiff2Base)[0];
        }

        $path = '';
        for($i = 0; $i < $previousCount - 1; $i++){
            $path .= '../';
        }

        $path .= implode('/', $targetDiff2Base);

        return $path;
    }


    public function makeSymLink($fromPath, $toPath)
    {
        if (!realpath($toPath)) {
            // 创建软连接
            symlink($fromPath, $toPath);
        }
    }
}
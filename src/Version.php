<?php
namespace geldek;

class Version {
    protected $major;
    protected $minor;
    protected $build;
    protected $revision;
    
    public function __construct($major=0, $minor=0, $build=0, $revision=0) {
        if(is_string($major)) {
            $parsed = $this->_parse($major);
            list($major, $minor, $build, $revision) = $parsed;
            
        }
        elseif($major instanceof Version) {
            $this->major = $major->major;
            $this->minor = $major->minor;
            $this->build = $major->build;
            $this->revision = $major->revision;

            return;
        }
        else {
            $this->_validateNumber($major);
            $this->_validateNumber($minor);
            $this->_validateNumber($build);
            $this->_validateNumber($revision);
        }
        
        $this->major = $major;
        $this->minor = $minor;
        $this->build = $build;
        $this->revision = $revision;
    }
    
    public function getMajor() {
        return $this->major;
    }
    
    public function getMinor() {
        return $this->minor;
    }
    
    public function getBuild() {
        return $this->build;
    }
    
    public function getRevision() {
        return $this->revision;
    }

    public function getMinorRevision() {
        return $this->revision & 0xFF;
    }
    
    public function getMajorRevision() {
        return $this->revision & 0xFF00 >> 8;
    }

    public function equals($version) {
        if($version instanceof Version) {
            return $this->major === $version->major 
                && $this->minor === $version->minor 
                && $this->build === $version->build 
                && $this->revision === $version->revision;
        }
        else {
            return false;
        }
    }
    
    public function compareTo($version) {
        if($version instanceof Version === false) {
            return false;
        }
        
        if($this->equals($version)) {
            return 0;
        }

        $cmp_major = $this->_compare($this->major, $version->major);
        if($cmp_major === 0) {
            $cmp_minor = $this->_compare($this->minor, $version->minor);
            if($cmp_minor === 0) {
                $cmp_build = $this->_compare($this->build, $version->build);
                if($cmp_build === 0) {
                    $cmp_revision = $this->_compare($this->revision, $version->revision);
                    return $cmp_revision;
                }
                else {
                    return $cmp_build;
                }
            }
            else {
                return $cmp_minor;
            }
        }
        else {
            return $cmp_major;
        }
    }
    
    public function toString($cnt = 4) {
        switch($cnt) {
            case 1:
                return (string)$this->major;
            case 2:
                return $this->major . '.' . $this->minor;
            case 3:
                return $this->major . '.' . $this->minor . '.' . $this->build;
            case 4:
                return $this->major . '.' . $this->minor . '.' . $this->build . '.' . $this->revision;
            default:
                return $this->major . '.' . $this->minor . '.' . $this->build . '.' . $this->revision;
        }
    }

    public static function parse($version) {
        return new self($version);
    }
    
    public static function tryParse($input, &$version) {
        try {
            $version = new self($input);
        } catch (\Exception $ex) {
            $version = null;
            return false;
        }
        
        return true;
    }

    private function _validateNumber($num) {
        if(preg_match('/^\d+$/', $num) === 1) {
            $int_num = (int)$num;
            if($int_num < 0 || $int_num > PHP_INT_MAX) {
                throw new \InvalidArgumentException('Version component is outside of integer bounds.');
            }
        }
        else {
            throw new \InvalidArgumentException('Version component is not a number.');
        }
    }
    
    private function _parse($version) {
        $numbers = [0,0,0,0];

        if(is_string($version)) {
            $version_split = explode('.', $version);
            $cnt = count($version_split);
            if($cnt < 2 || $cnt > 4) {
                throw new \InvalidArgumentException('Version has fewer than two components or more than four components');
            }

            $this->_validateNumber($version_split[0]);
            $numbers[0] = (int)$version_split[0];

            $this->_validateNumber($version_split[1]);
            $numbers[1] = (int)$version_split[1];
            
            if(isset($version_split[2])) {
                $this->_validateNumber($version_split[2]);
                $numbers[2] = (int)$version_split[2];
            }
            else {
                $numbers[2] = 0;
            }
            
            if(isset($version_split[3])) {
                $this->_validateNumber($version_split[3]);
                $numbers[3] = (int)$version_split[3];
            }
            else {
                $numbers[3] = 0;
            }
        }
        else {
            throw new \InvalidArgumentException('Invalid type.');
        }

        return $numbers;
    }

    private function _compare($left, $right) {
        if($left < $right) {
            return -1;
        }
        elseif($left > $right) {
            return 1;
        }
        else {
            return 0;
        }
    }
}
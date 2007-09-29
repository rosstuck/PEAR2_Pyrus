<?php
abstract class PEAR2_Pyrus_Package_Base implements PEAR2_Pyrus_IPackage
{
    protected $packagefile;
    /**
     * The original source of this package
     *
     * This is a chain documenting the steps it took to get this
     * package instantiated, for instance Tar->Abstract
     * @var PEAR2_Pyrus_IPackage
     */
    protected $from;

    function __construct(PEAR2_Pyrus_PackageFile $packagefile)
    {
        $this->packagefile = $packagefile;
    }

    function setFrom(PEAR2_Pyrus_IPackage $from)
    {
        $this->from = $from;
    }

    /**
     * Create vertices/edges of a directed graph for dependencies of this package
     *
     * Iterate over dependencies and create edges from this package to those it
     * depends upon
     * @param PEAR2_Pyrus_DirectedGraph $graph
     * @param array $packages channel/package indexed array of PEAR2_Pyrus_Package objects
     */
    function makeConnections(PEAR2_Pyrus_DirectedGraph $graph, array $packages)
    {
        foreach (array('required', 'optional') as $requred) {
            foreach (array('package', 'subpackage') as $package) {
                foreach ($this->dependencies->$required->$package as $d) {
                    if (isset($d['conflicts'])) {
                        continue;
                    }
                    $dchannel = isset($d['channel']) ?
                        $d['channel'] :
                        '__uri';
                    if (isset($packages[$dchannel . '/' . $d['name']])) {
                        $graph->connect($this, $packages[$dchannel . '/' . $d['name']]);
                    }
                }
            }
        }
        foreach ($this->dependencies->group as $group) {
            foreach (array('package', 'subpackage') as $package) {
                foreach ($this->dependencies->$required->$package as $d) {
                    if (isset($d['conflicts'])) {
                        continue;
                    }
                    $dchannel = isset($d['channel']) ?
                        $d['channel'] :
                        '__uri';
                    if (isset($packages[$dchannel . '/' . $d['name']])) {
                        $graph->connect($this, $packages[$dchannel . '/' . $d['name']]);
                    }
                }
            }
        }
    }

    function offsetExists($offset)
    {
        return $this->packagefile->info->hasFile($offset);
    }

    function offsetGet($offset)
    {
        if (strpos($offset, 'contents://') === 0) {
            return $this->getFileContents(substr($offset, 11));
        }
        return $this->packagefile->info->getFile($offset);
    }

    function offsetSet($offset, $value)
    {
        return;
    }

    function offsetUnset($offset)
    {
        return;
    }

    function current()
    {
        return key($this->packagefile->info->_packageInfo['filelist']);
    }

    function key()
    {
        return 1;
    }

    function next()
    {
        next($this->packagefile->info->_packageInfo['filelist']);
    }

    function rewind()
    {
        reset($this->packagefile->info->_packageInfo['filelist']);
    }

    function __call($func, $args)
    {
        // delegate to the internal object
        return call_user_func_array(array($this->packagefile->info, $func), $args);
    }

    function __get($var)
    {
        return $this->packagefile->info->$var;
    }

    function __toString()
    {
        return $this->packagefile->__toString();
    }

    function valid()
    {
        return key($this->packagefile->info->_packageInfo['filelist']);
    }
}
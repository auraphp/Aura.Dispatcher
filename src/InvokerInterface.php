<?php
namespace Aura\Invoker;

interface InvokerInterface
{
    public function __invoke(array $params = []);
    public function setObjectParam($object_param);
    public function getObjectParam();
    public function setObjects(array $objects);
    public function addObjects(array $objects);
    public function getObjects();
    public function setObject($name, $object);
    public function hasObject($name);
    public function getObjectByName($name);
    public function getObjectByParams(array $params);
}

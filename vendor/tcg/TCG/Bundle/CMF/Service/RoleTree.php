<?php

namespace TCG\Bundle\CMF\Service;

use TCG\Bundle\CMF\Database\MySQL\Model\Role;
use TCG\Component\Util\StringUtil;

class RoleTree
{
    /**
     * @var TreeNode
     */
    protected $root;

    /**
     * RoleTree constructor.
     * @param Role[] $roles
     */
    public function __construct(array $roles)
    {
        $nodes = [];
        foreach ($roles as $role) {
            $nodes[$role->id] = new TreeNode($role);
        }
        $root = $roles[0];
        $this->root = new TreeNode($root);
        $this->preOrderTree2RecursiveTree($nodes);
    }



    /**
     * @param TreeNode[] $items
     * @param TreeNode|null $node
     */
    private function preOrderTree2RecursiveTree(array $items, TreeNode $node = null)
    {
        if (!$node) {
            $node = $this->root;
        }
        $nodeDepth = $node->depth;
        $nodeLeft = $node->leftValue;
        $nodeRight = $node->rightValue;
        $nodeChildren = $node->children;
        if (!$nodeChildren) {
            $nodeChildren = [];
        }
        foreach ($items as $key => $item) {
            $itemDepth = $item->depth;
            $itemLeft = $item->leftValue;
            $itemRight = $item->rightValue;
            if ($itemDepth == $nodeDepth + 1 && $itemLeft > $nodeLeft && $itemRight < $nodeRight) {
                $nodeChildren[] = $item;
                unset($items[$key]);
            }
        }
        if ($items) {
            foreach ($nodeChildren as $childNode) {
                $this->preOrderTree2RecursiveTree($items, $childNode);
            }
        }
        $node->children = $nodeChildren;
    }

    /**
     * @return TreeNode
     */
    public function getRoot()
    {
        return $this->root;
    }


    public function toArray()
    {
        return $this->root->toArray();
    }
}

/**
 * Class TreeNode
 * @package TCG\Bundle\CMF\Service
 * @property $key
 * @property $name
 * @property $description
 * @property $createAt
 * @property $updateAt
 * @property $depth
 * @property $leftValue
 * @property $rightValue
 */
class TreeNode
{
    /**
     * @var TreeNode[]
     */
    public $children = [];

    public $depth = 0;

    public function __construct(Role $role)
    {
        $data = $role->toArray();
        foreach ($data as $key => $value) {
            $property = $role->key2Property($key);
            $this->{$property} = $value;
        }
    }


    public function toArray()
    {
        $return = [];
        $rc = new \ReflectionClass($this);
        foreach ($rc->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $key = StringUtil::underscore($property);
            $return[$key] = $this->{$property};
        }
        // children
        $children = [];
        foreach ($this->children as $treeNode) {
            $children[] = $treeNode->toArray();
        }
        $return['children'] = $children;
        return $return;
    }
}
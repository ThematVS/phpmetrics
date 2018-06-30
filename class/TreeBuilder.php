<?php

class TreeBuilder
{
    private static $defaultOptions = [
        'showFiles' => false
    ];
    private static $excludeDir = [
        'node_modules', 'public', 'scss',
        'documentation', 'react', 'views', 'configs',
        '*images*', '*css*', '*js*',
    ];

    private static function parseTree($tree, $indentor = '*')
    {
        $parsedTree = [];
        $cLevel = -1;
        $currentArray = &$parsedTree;
        $parent = [];
        $i = -1;

        foreach ($tree as $dir) {
            $i++;
            $nLevel = substr_count($dir, $indentor);

            $name = substr($dir, $nLevel);

            if ($nLevel > $cLevel) {
                $parent[] = &$currentArray;
                $currentArray[$name] = [];
                $currentArray = &$currentArray[$name];
                $cLevel = $nLevel;
            } elseif ($nLevel < $cLevel) {
                $cnt = sizeof($parent) - ($cLevel - $nLevel);
                $oldParent = &$parent[$cnt - 1];
                $parent = array_slice($parent, 0, $cnt);
                $oldParent[$name] = [];
                $currentArray = &$oldParent[$name];
                $cLevel = $nLevel;
            } else {
                $oldParent = &$parent[sizeof($parent) - 1];
                array_pop($parent);
                $oldParent[$name] = [];
                $parent[] = &$oldParent;
                $currentArray = &$oldParent[$name];
            }
//            if ($i == 20) break;
        }
        return $parsedTree;
    }

    public static function buildHTMLTree($tree, $options = [])
    {
        $options = array_merge(self::$defaultOptions, $options);

        $parsedTree = self::parseTree(explode("\n", trim($tree)));

        $showDir = function ($name, $putEndTag = true, $flags = []) {
            $metric = '';
            if (empty($flags['exclude'])) {
                $metric = '<a href="#" title="run metrics">run</a>';
            }
            if (!empty($flags['exists'])) {
                $metric = '<a href="#" title="view metrics">view</a>';
            }

            return '<li data-text="'.$name.'">'.$name.' <span class="details">'.$metric.'</span>'.($putEndTag ? '</li>' : '');
        };
        $shouldExclude = ['TreeBuilder', 'shouldExclude'];
        $builder = function ($node, $level = -1) use (&$builder, $showDir, $shouldExclude) {
            $out = [];
            $level++;

            if (!empty(array_keys($node))) {
                foreach ($node as $dirname => $subnode) {
                    $flags = [
                        'exclude' => call_user_func_array($shouldExclude, [$dirname])
                    ];
                    $out[] = $showDir($dirname, $flags);

                    if (!empty($subnode)) {
                        $out[] = $builder($subnode, $level);
                        $out[] = '</li>';
                    }
                }
            }
            return '<ul>'.join(PHP_EOL, $out).'</ul>';
        };

        return $builder($parsedTree);
    }

    private static function shouldExclude($dirname)
    {
        return false;
        foreach (self::$excludeDir as $pattern) {
            $pattern = str_replace('.', '\.', $pattern);
            $pattern = str_replace('*', '.*', $pattern);
            $pattern = '~'.$pattern.'~';
echo $pattern, PHP_EOL;
            if (preg_match($pattern, $dirname)) { return true; }
        }
        return false;
    }
}

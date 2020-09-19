<?php
declare(strict_types=1);
/**
 * Created by PhpStorm.
 * User: bardo
 * Date: 2020-09-06
 * Time: 10:45
 */

namespace Bardoqi\Sight\Map;

use Bardoqi\Sight\Abstracts\AbstractList;
use Bardoqi\Sight\Enums\JoinTypeEnum;
use Bardoqi\Sight\Enums\RelationEnum;


/**
 * Class MultiMap
 *
 * @package Bardoqi\Sight\Abstracts
 */
class MultiMap extends AbstractList
{
    /**
     * $keyed_by : Indicating which key is used for sort;
     *
     * @var string
     * @access public
     */
    public $keyed_by = '';

    /**
     * $join_type : Keep the join type.
     *
     * @var int
     * @access public
     */
    public $join_type = 0;

    /**
     * @var array
     */
    private $empty_item = null;

    /**
     * Create a instance
     *
     * @param null|array $data
     * @param null|string $keyed_by
     * @return static
     * @static
     */
    public static function of($data = null,$keyed_by = null,$join_type=JoinTypeEnum::INNER_JOIN){
        $instance = new static();
        $instance->join_type = $join_type;
        $instance->keyed_by = $keyed_by;
        if(null !== $keyed_by){
            foreach($data as $item){
                $instance->data[$item[$keyed_by]][] = $item;
            }
            return $instance;
        }
        $instance->data = $data;
        return $instance;
    }

    /**
     * @return array
     */
    public function getAnyOne(){
        $item = reset($this->data);
        if(isset($item[0]))
            return $item[0];
        return $item;
    }

    /**
     * Create a empty row with given offset
     * @param bool $is_single
     *
     * @return array
     */
    public function getEmptyOne($is_single){
        if(null === $this->empty_item){
            $item = $this->getAnyOne();
            if(is_array($item)){
                $keys = array_keys($item);
                $values = array_fill(0, count($keys), '');
                $this->empty_item = array_combine($keys, $values);
            }else{
                $this->empty_item = [];
            }
        }
        if($is_single){
            return SingleMapItem::of($this->empty_item,$this->keyed_by,0);
        }
        return MultiMapItem::of($this->empty_item,$this->keyed_by,RelationEnum::HAS_MANY);
    }

    /**
     * @return \Generator
     */
    public function getLocalOne(){
        foreach($this->data as $key => $item){
            yield $key => SingleMapItem::of($item,$this->keyed_by,0);
        }
    }

    /**
     * @param $offset
     *
     * @return \Bardoqi\Sight\Map\SingleMapItem|null
     */
    public function getHasOne($offset){
        if(!isset($this->data[$offset])){
            if(JoinTypeEnum::INNER_JOIN === $this->join_type){
                return null;
            }
            return SingleMapItem::of($this->getEmptyOne(true),$this->keyed_by,0);
        }
        return SingleMapItem::of($this->data[$offset][0],$this->keyed_by,0);
    }

    /**
     * @param $offset
     *
     * @return \Bardoqi\Sight\Map\MultiMapItem|null
     */
    public function getHasManyMerge($offset){
        if(!isset($this->data[$offset])){
            if(JoinTypeEnum::INNER_JOIN === $this->join_type){
                return null;
            }
            $item[0] = $this->getEmptyOne(false);
            return MultiMapItem::of($item,$this->keyed_by,RelationEnum::HAS_MANY_MERGE);
        }
        return MultiMapItem::of($this->data[$offset],$this->keyed_by,RelationEnum::HAS_MANY_MERGE);
    }

    /**
     * @param $offset
     *
     * @return \Bardoqi\Sight\Map\MultiMapItem|null
     */
    public function getHasManySplit($offset_values){
        $offsets = explode(',',$offset_values);
        $item = [];
        foreach($offsets as $offset){
            if((!isset($this->data[$offset])) || (!isset($this->data[$offset][0]))){
                if(JoinTypeEnum::INNER_JOIN === $this->join_type){
                    continue;
                }
                $item[] = $this->getEmptyOne(false);
            }else{
                $join_data = $this->data[$offset];
                $item[$offset] = $join_data[0];
            }
        }
        return MultiMapItem::of($item,$this->keyed_by,RelationEnum::HAS_MANY_SPLIT);;
    }

    /**
     * @param $offset
     *
     * @return bool|\Generator
     */
    public function getItems($offset){
        if(!isset($this->data[$offset])){
            return [];
        }
        foreach($this->data[$offset] as $key => $item){
            yield $key => MultiMapItem::of($item,$this->keyed_by,0);
        }
    }

    /**
     * @return \Generator
     */
    public function listItems(){
        foreach($this->data as $key => $item){
            yield $key => MultiMapItem::of($item,$this->keyed_by,0);
        }
    }

    /**
     * @return \Generator
     */
    public function singleListItems(){
        foreach($this->data as $key => $item){
            yield $key => SingleMapItem::of($item,$this->keyed_by,0);
        }
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function hasColumn($name){
    	 // get the first item.
        $item = reset($this->data);
        // if keyed_by, it is a  multimap, so we must get first item.
        if(!empty($this->keyed_by)){
        	// if there is no data, the $item would be false
        	if(false === $item){
        		$this->data[0][$name]='';
        		return true;
        	}
             $item = reset($item);
        }
        return isset($item[$name]);
    }
}

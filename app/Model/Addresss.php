<?php
/*
 * @Author: your name
 * @Date: 2019-12-23 15:36:53
 * @LastEditTime : 2019-12-23 15:37:41
 * @LastEditors  : Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: \htdocs\blog\app\Model\Addresss.php
 */

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Addresss extends Model
{
    //
    public $primaryKey = 'address_id';
    /**
     * 可以被批量赋值的属性.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * 关联到模型的数据表
     *
     * @var string
     */
    protected $table = 'addresss';

    /**
     * 表明模型是否应该被打上时间戳
     *
     * @var bool
     */
    public $timestamps = false;
}

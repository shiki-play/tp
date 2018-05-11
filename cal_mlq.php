<style>
   .com{
       margin-left: 2em;
   }
</style>
<h2>我的回合抽牌</h2>
<h3>面灵气抽取模拟器</h3>
<p>抽取时会稍微花点时间，浏览器转圈圈是正常现象</p>
<form method="post" action="">
    <label>我准备抽<input type="text" name="draws">张蓝票票</label><br>
    <p>是否全收集</p>
    是：
    <input type="radio" checked="checked" name="EU" value="1" />
    <br />
    否：
    <input type="radio" name="EU" value="0" />
    <input type="submit" class="com" value="开抽">
</form>

<?php

class cal_mlq{        //cal_mlq用来计算单次n抽是否出面灵气

    private $EU;  //ouqi
    private $draws;  //抽几下
    public $mlq= false;//是否抽出了面灵气
    public $ssr=0; //抽了几个ssr出来

    function __construct($draws,$EU)
    {
        $this->draws=$draws;    //定义可以抽多少次
        $this->EU = $EU;
    }
    public function rate($chance,$total){    //单纯的roll点
        if(mt_rand(1,$total)<=$chance){
            return true;
        }else{
            return false;
        }
    }

    public function mlq($EU){
          if($EU == 1){
                            if($this->rate(2,10)){   //全收集加成 面灵气出率为20%  一般为18/1
                                $this->mlq=true;
                            }
          }else{
                            if($this->rate(1,18)){   // 一般为18/1
                                $this->mlq=true;
                            }
                        }
    }
    public function cal_mlq(){        //计算是否抽出了面灵气
        for($i=1;$i<=$this->draws;$i++){
            $case='default';     //初始化抽卡模式
            if($this->mlq){
                $case='out';     //抽出了面灵气跳出循环
            }elseif ($this->ssr<3){
                $case='default';  // 2.5倍出率抽卡模式
            }elseif ($this->ssr>=3){
                $case='over';    // 1.2%ssr出率
            }

            switch ($case){
                case 'out':
                    break;
                case 'over':
                    if($this->rate(12,1000)){   // 1.2%ssr出率
                        $this->ssr++;
                      $this->mlq($this->EU);

                    }
                    break;
                case 'default'   :
                    if($this->rate(30,1000)){   // 2.5倍出率抽卡模式
                        $this->ssr++;
                        $this->mlq($this->EU);
                    }
                    break;
            }
            if($this->mlq){    //抽出来了面灵气，不再计算
                break;
            }
        }
        if($this->mlq){
            return true;  //yeah! 出了~
        }else{
            return false;  //fuck ! 沉了
        }
    }

}

if(isset($_POST['fuck'])){
    $mlq=0;   //初始化
    $num=10000;   //来个一万次
    $draws=300;
    for($i=1;$i<$num;$i++){
        $a = new cal_mlq($draws,1);
        if($a->cal_mlq()){
            $mlq++;
        }
    }
    print '没有全收集'.$draws.'抽 重复抽了'.$num.'次.有'.$mlq.'次出了面灵气,几率为'.($mlq/$num*100).'\%';

}
if(isset($_POST['draws'])){
   $age = filter_input(INPUT_POST,'draws',FILTER_VALIDATE_INT);
   if($age === false){
       print '抽取数必须为整数';
   }elseif(trim($_POST['draws'])>=1001){
       print '抽取数最多为1千';
   }else{
       $draws =trim($_POST['draws']);
       $num=10000;
       $mlq=0;
       $EU = $_POST['EU'];
       for($i=1;$i<$num;$i++){
           $a = new cal_mlq($draws,$EU);
           if($a->cal_mlq()){
               $mlq++;
           }
       }
       if($EU){
           print '全收集'.$draws.'抽 重复抽了'.$num.'次.有'.$mlq.'次出了面灵气,几率为'.($mlq/$num*100).'\%';
       }else{
           print '没有全收集'.$draws.'抽 重复抽了'.$num.'次.有'.$mlq.'次出了面灵气,几率为'.($mlq/$num*100).'\%';
       }

   }
}


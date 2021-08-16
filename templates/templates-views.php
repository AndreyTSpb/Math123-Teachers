<?php
/**
 * Created by PhpStorm.
 * User: andrey
 * Date: 13/04/2021
 * Time: 13:01
 */
?>
<!-- НАШИ ПРЕПОДАВАТЕЛИ -->
<section class="our-teacher">
        <h4><?php if($data['title']):?>
                <?=$data['title'];?>
            <?php else:?>
                наши преподаватели
            <?php endif;?>
        </h4>
    <div class="our-teacher-wraper">
        <?php if(isset($data['teachers_info']) AND !empty($data['teachers_info'])):?>
            <?php foreach ($data['teachers_info'] as $id_teach => $teacher):?>
                <div class="our-teacher-item">
                    <div class="our-teacher-item-grid">
                        <img class="purple-frame radius" src="<?=$teacher['photo'];?>" alt="">
                        <div class="our-teacher-item-name">
                            <?=$teacher['name'];?>
                        </div>
                        <div class="our-teacher-item-info">
                            Преподаватель: <?=$teacher['subject'];?>.<br>
                            Стаж работы-5 лет
                            <a href="/nashi-prepodavateli">подробней.</a>
                        </div>
                        <a href = "" class="our-teacher__button btn-sub">
                            Подробней
                        </a>
                    </div>
                </div>
            <?php endforeach;?>
        <?php endif;?>
    </div>
</section>

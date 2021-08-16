<?php
/**
 * Plugin Name: Math123 Teachers
 * Description: WordPress плагин для отображения слайдера с преподпвателями. Для вставки плагина на странице использовать шорт-код [Math123_Teachers title='название блока' slidesToShow='3' slidesToScroll='1' autoplay='1' autoplaySpeed='1000']tyt[/Math123_Teachers]. Плагин получает данные преподавателей из постов в категории "Преподаватели".
 * Plugin URI: https://github.com/AndreyTSpb/Math123-Teachers
 * Author: Andrey Tynyany
 * Version: 1.0.1
 * Author URI: http://tynyany.ru
 *
 * Text Domain: Math123 Teachers
 *
 * @package Math123 Teachers
 */

defined('ABSPATH') or die('No script kiddies please!');

define( 'WPM123TS_VERSION', '1.0.1' );

define( 'WPM123TS_REQUIRED_WP_VERSION', '5.5' );

define( 'WPM123TS_PLUGIN', __FILE__ );

define( 'WPM123TS_PLUGIN_BASENAME', plugin_basename( WPM123TS_PLUGIN ) );

define( 'WPM123TS_PLUGIN_NAME', trim( dirname( WPM123TS_PLUGIN_BASENAME ), '/' ) );

define( 'WPM123TS_PLUGIN_DIR', untrailingslashit( dirname( WPM123TS_PLUGIN ) ) );

define( 'WPM123TS_PLUGIN_URL',
    untrailingslashit( plugins_url( '', WPM123TS_PLUGIN ) )
);

/**
 *  Переменная куда помещать данные полученые
 *  данные берем из постов
 */
$teachers_data = array();

/**
 * Регистрация шорт кода
 */
add_shortcode('Math123_Teachers', 'math123_teachers');


function math123_teachers($atts, $content){
    global $teachers_data;

    /**
     * Подключили скрипт для обработки
     */
    add_action('wp_footer', 'math123_teachers_script');

    if(isset($atts['title']) AND !empty($atts['title'])){
        $teachers_data['title'] = $atts['title'];
    }else{
        $teachers_data['title'] = false;
    }
    /**
     * Настройки для слайдера полученые из шорт-кода
     */
    //slidesToShow - количество слайдов
    if(isset($atts['slidestoshow']) AND !empty($atts['slidestoshow'])){
        $teachers_data['setting_for_slick_slider']['slidesToShow'] = $atts['slidestoshow'];
    }else{
        $teachers_data['setting_for_slick_slider']['slidesToShow'] = 1;
    }
    //slidesToScroll - количество прокрученаний за раз
    if(isset($atts['slidestoscroll']) AND !empty($atts['slidestoscroll'])){
        $teachers_data['setting_for_slick_slider']['slidesToScroll'] = $atts['slidestoscroll'];
    }else{
        $teachers_data['setting_for_slick_slider']['slidesToScroll'] = 1;
    }
    //autoplay - автопрокрутка
    if(isset($atts['autoplay']) AND !empty($atts['autoplay'])){
        $teachers_data['setting_for_slick_slider']['autoplay'] = $atts['autoplay'];
    }else{
        $teachers_data['setting_for_slick_slider']['autoplay'] = 1;
    }
    //autoplaySpeed - скорость автопрокрутки
    if(isset($atts['autoplayspeed']) AND !empty($atts['autoplayspeed'])){
        $teachers_data['setting_for_slick_slider']['autoplaySpeed'] = $atts['autoplayspeed'];
    }else{
        $teachers_data['setting_for_slick_slider']['autoplaySpeed'] = 1000;
    }

    /**
     * Заполним данные для формирования блоков, полученные из постов
     */
    $teachers_data['teachers_info'] = math123_teacher_get_posts();

    /**
     * Получим буфиризованый вывод шаблона
     */
    $html = math123_teachers_get_html();
    return $html;
}

/**
 * Вывод шаблона
 */
function math123_teachers_get_html(){
    global $teachers_data;
    $data = $teachers_data;
    ob_start();
    include WPM123TS_PLUGIN_DIR."/templates/templates-views.php";
    $html = ob_get_contents();
    ob_end_clean();
    return $html;
}

/**
 * Пулучаем данные о преподе из постов
 */
function math123_teacher_get_posts(){
    // для преподавателей категория 5
    // параметры по умолчанию
    $posts = get_posts( array(
        'numberposts' => -1,
        'category'    => 5,
        'orderby'     => 'date',
        'order'       => 'ASC',
        'post_type'   => 'post',
        'suppress_filters' => true, // подавление работы фильтров изменения SQL запроса
    ) );
    /**
     * Получаем ссылку на категорию
     */
    $cat_link = get_category_link(5);

    /**
     * OBJECT
     * [post_author] => 1
     * [post_date] => 2021-04-13 14:18:27
     * [post_date_gmt] => 2021-04-13 11:18:27
     * [post_content] => Контент страницы
     * [post_title] => Иванова Мария
     * [post_excerpt] => ИТМО, к.м.н., зав. кафедрой... - описание
     * [post_status] => publish
     * [comment_status] => closed
     * [post_name] => ivanova-mariya
     * [guid] => http://math123-wordpress.local/?p=205
     *
     */
    $teachers_arr = array();
    foreach ($posts as $id_arr => $post){

        /**
         * Вместо линка на конкретную страницу препода $post->guid, 
         * выводим линк на страницу категорий $cat_link
         */
        $teach = math123_teacher_get_custom_fields($post->ID);
        $teachers_arr[$post->ID] = array(
            "photo"  => $teach['img'],
            "rating" => $teach['rating'],
            "subject"=> $teach['subject'],
            "name"   => $post->post_title,
            "desc"   => $post->post_excerpt,
            "link"   => $cat_link
        );
    }
    wp_reset_postdata(); // сброс
    return $teachers_arr;
}


/**
 * Получаем кастомпые поля к записе
 */
function math123_teacher_get_custom_fields($id){
    $fields = get_fields($id);
    /**
     * ARRAY
     * [Fotoprepodavatelya] =>[
     *      [ID] => 211,
     *      [id] => 211,
     *      [title] => иванова мария,
     *      [filename] => teacher.png,
     *      [url] => http://math123-wordpress.local/wp-content/uploads/2021/04/teacher.png,
     *      [link] => http://math123-wordpress.local/teachers/ivanova-mariya/attachment/teacher/
     *      [description] => иванова мария
     *      [uploaded_to] => 205,
     *      .....
     * ],
     * [Predmetkotoryjprepodaet] => Математика,
     * [Rejtingprepodavatelya] => 3
     *
     */

    $new_arr = array(
        'img' => $fields['Fotoprepodavatelya']['url'],
        'subject' => $fields['Predmetkotoryjprepodaet'],
        'rating' => math123_teacher_rating($fields['Rejtingprepodavatelya'])
    );
    return $new_arr;
}

/**
 * Заполнение рейтинга звездами
 */
function math123_teacher_rating($qnt_stars){
    $str = '';
    for($i = 1; $i < 6; $i++){
        if($i <= $qnt_stars){
            $str .= '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16"><path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.283.95l-3.523 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/></svg>';
        }else{
            $str .='<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-star" viewBox="0 0 16 16"><path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.523-3.356c.329-.314.158-.888-.283-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767l-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288l1.847-3.658 1.846 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.564.564 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z"/></svg>';
        }
    }
    return $str;
}

/**
 * Подключение скриптов
 */
function math123_teachers_script(){
    //code
    global $teachers_data;
    $js_data = $teachers_data['setting_for_slick_slider'];

    wp_register_style( 'math123TeachersCss', plugins_url( 'assets/css/custom.css', __FILE__ ));

    wp_enqueue_style( 'math123TeachersCss');

    /**
     * регистрация скриптов
     */
    wp_register_script('math123TeachersScript', plugins_url( 'assets/js/scripts.js', __FILE__ ));
    /**
     * подключение скриптов
     */
    //wp_enqueue_script('yandexMapScript');
    wp_enqueue_script('math123TeachersScript');
    /**
     * Параматры для скрипта
     */
    wp_localize_script( 'math123TeachersScript', 'Obj', $js_data );
}
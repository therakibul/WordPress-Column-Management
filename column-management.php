<?php
/*
 * Plugin Name:       Column Management
 * Plugin URI:        https://example.com/plugins/the-basics/
 * Description:       Handle the basics with this plugin.
 * Version:           1.10.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            John Smith
 * Author URI:        https://author.example.com/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       clm
 * Domain Path:       /languages
*/

    function clm_manage_post_columns($column){
        unset($column["date"]);
        unset($column["author"]);
        unset($column["comments"]);
        unset($column["tags"]);
        unset($column["categories"]);
        $column["id"] = "ID";
        $column["thumbnail"] = "Thumbnail";
        $column["wordcount"] = "Word Count";
        return $column;
    }
    add_filter("manage_posts_columns", "clm_manage_post_columns");

    function clm_manage_posts_custom_column($column, $post_id){
        if("id" == $column){
            echo $post_id;
        }elseif("thumbnail" == $column){
            $thumbnail = get_the_post_thumbnail($post_id, array(100, 100));
            echo $thumbnail;
        }elseif("wordcount" == $column){
            $post = get_post($post_id);
            // $wordn = str_word_count(strip_tags($post->post_content));
            $wordn = get_post_meta($post_id, "wordn", true);
            echo $wordn;
        }
    }
    add_action("manage_posts_custom_column", "clm_manage_posts_custom_column", 10, 2);

    function clm_manage_sortable_column($column){
        $column["wordcount"] = "wordn";
        return $column;
    }
    add_filter("manage_edit-post_sortable_columns", "clm_manage_sortable_column");

    // function clm_set_wordn_as_meta_value(){
    //     $posts = get_posts(array(
    //         "post_type" => "post",
    //         "posts_per_page" => -1
    //     ));
    //     foreach($posts as $p){
    //         $post = get_post($p->ID);
    //         $wordn = str_word_count(strip_tags($post->post_content));
    //         add_post_meta($p->ID, "wordn", $wordn );
    //     }
    // }
    // add_action("init", "clm_set_wordn_as_meta_value");

    function clm_sort_wc_column($wpq){
        $orderby = $wpq->get("orderby");
        if("wordn" == $orderby){
            $wpq->set("meta_key", "wordn");
            $wpq->set("orderby", "meta_value_num");
        }
    }
    add_action("pre_get_posts", "clm_sort_wc_column");

    function clm_update_wordcount($post_id){
        $post = get_post($post_id);
        $wordn = str_word_count(strip_tags($post->post_content));
        update_post_meta($post_id, "wordn", $wordn);
    }
    add_action("save_post", "clm_update_wordcount");

    function cld_demo_filter(){
        if(isset($_REQUEST["post_type"]) && $_REQUEST["post_type"] != "post"){
            return;
        }
        $filter_value = $_REQUEST["DEMOFILTER"] ?? "";
        $values = [
            "0" => "Demo Filter",
            "1" => "Filter",
            "2" => "Filter++" 
        ];
        echo '<select name="DEMOFILTER">';
        foreach($values as $value => $label){
            $selected = "";
            if($filter_value == $value){
                $selected = "selected";
            }
            printf("<option value='%s' %s>%s</option>", $value, $selected, $label);

        }
        echo '</select>';
    }
    add_action("restrict_manage_posts", "cld_demo_filter");

    function cld_demo_filter_process($wpq){
        $filter_value = $_REQUEST["DEMOFILTER"] ?? "";
        if("1" == $filter_value){
            $wpq->set("post__in", array(1));
        }elseif("2" == $filter_value){
            $wpq->set("post__in", array(6, 9, 16));
        }
    }
    add_action("pre_get_posts", "cld_demo_filter_process");


    function cld_thumbnail_filter(){
        if(isset($_REQUEST["post_type"]) && $_REQUEST["post_type"] != "post"){
            return;
        }
        $filter_value = $_REQUEST["THUMFILTER"] ?? "";
        $values = [
            "0" => "Thumbnail Filter",
            "1" => "Has Thumbnail",
            "2" => "No Thumbnail" 
        ];
        echo '<select name="THUMFILTER">';
        foreach($values as $value => $label){
            $selected = "";
            if($filter_value == $value){
                $selected = "selected";
            }
            printf("<option value='%s' %s>%s</option>", $value, $selected, $label);

        }
        echo '</select>';
    }
    add_action("restrict_manage_posts", "cld_thumbnail_filter");


    function cld_demo_thumbnail_process($wpq){
        $filter_value = $_REQUEST["THUMFILTER"] ?? "";
        if("1" == $filter_value){
            $wpq->set("meta_query", array(
                array(
                    "key" => "_thumbnail_id",
                    "compare" => "EXISTS"
                )
            ));
        }elseif("2" == $filter_value){
            $wpq->set("meta_query", array(
                array(
                    "key" => "_thumbnail_id",
                    "compare" => "NOT EXISTS"
                )
            ));
        }
    }
    add_action("pre_get_posts", "cld_demo_thumbnail_process");


    function cld_wordcount_filter(){
        if(isset($_REQUEST["post_type"]) && $_REQUEST["post_type"] != "post"){
            return;
        }
        $filter_value = $_REQUEST["WCFILTER"] ?? "";
        $values = [
            "0" => "Word Count Filter",
            "1" => "Avobe 100",
            "2" => "Between 50-100",
            "3" => "Below 50"
        ];
        echo '<select name="WCFILTER">';
        foreach($values as $value => $label){
            $selected = "";
            if($filter_value == $value){
                $selected = "selected";
            }
            printf("<option value='%s' %s>%s</option>", $value, $selected, $label);

        }
        echo '</select>';
    }
    add_action("restrict_manage_posts", "cld_wordcount_filter");

    function cld_demo_wordcount_filter_process($wpq){
        $filter_value = $_REQUEST["WCFILTER"] ?? "";
        if("1" == $filter_value){
            $wpq->set("meta_query", array(
                array(
                    "key" => "wordn",
                    "value" => 100,
                    "compare" => ">=",
                    "type" => "NUMERIC"
                )
            ));
        }elseif("2" == $filter_value){
            $wpq->set("meta_query", array(
                array(
                   "key" => "wordn",
                    "value" => array(50, 100),
                    "compare" => "between",
                    "type" => "NUMERIC"
                )
            ));
        }elseif("3" == $filter_value){
            $wpq->set("meta_query", array(
                array(
                    "key" => "wordn",
                    "value" => 50,
                    "compare" => "<=",
                    "type" => "NUMERIC"
                )
            ));
        }
    }
    add_action("pre_get_posts", "cld_demo_wordcount_filter_process");
?>
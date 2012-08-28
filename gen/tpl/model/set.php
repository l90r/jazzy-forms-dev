<?
    $schema = get_columns($table);
    $placeholder_array = array();
    $assignment_array = array();
    $member_array = array();
    $column_array = array();
    foreach($schema as $column => $definition) {
        $type = $definition['type'];
        $placeholder = get_placeholder($type);
        $assignment_array[] = "`{$column}`={$placeholder}";
        $placeholder_array[] = $placeholder;
        $member_array[] = '$obj->' . $column;
        $column_array[] = '`' . $column  . '`';
    }
    $placeholders = implode(',', $placeholder_array);
    $assignments = implode(',', $assignment_array);
    $members = implode(',', $member_array);
    $columns = implode(',', $column_array);
?>

function jzzf_<?=$method?>($obj) {
    global $wpdb;
    if($obj->id) {
        $query = "UPDATE {$wpdb->prefix}jzzf_<?=$table?> SET <?=$assignments?> WHERE id=%d";
        $sql = $wpdb->prepare($query, <?=$members?>, $obj->id);
        jzzf_debug("SQL (<?=$method?>): " . $sql);
        $result = $wpdb->query($sql);
        $id = $obj->id;
    } else {
        $query = "INSERT INTO {$wpdb->prefix}jzzf_<?=$table?> (<?=$columns?>) VALUES (<?=$placeholders?>)";
        $sql = $wpdb->prepare($query, <?=$members?>);
        $result = $wpdb->query($sql);
        $id = $wpdb->insert_id;
    }
<? if($one_to_many || $one_to_one): ?>
    if($result !== false) {
<? foreach($one_to_many as $id => $child ) : ?>
        if(is_array($obj-><?=$id?>)) {
            $placeholders = array();
            $values = array();
            foreach($obj-><?=$id?> as $child) {
                $placeholders[] = '%d';
                $values[] = $child->id;
            }
            $query = "SELECT id FROM {$wpdb->prefix}jzzf_<?=$child?> WHERE `<?=$table?>` = %d";
            if($placeholders) {
                $query .= ' AND id NOT IN (' . implode(',', $placeholders) . ')';
            }
            array_unshift($values, $obj->id);
            $sql = $wpdb->prepare($query, $values);
            jzzf_debug("SQL2 (<?=$method?>): " . $sql);
            $orphans = $wpdb->get_col($sql);
            foreach($orphans as $orphan) {
                jzzf_delete_<?=$child?>($orphan);
            }
            foreach($obj-><?=$id?> as $child) {
                $child-><?=$table?> = $id;
                jzzf_set_<?=$child?>($child);
            }
        }
<? endforeach ?>
<? foreach($one_to_one as $id => $child ) : ?>
        $previous = jzzf_get_<?=$child?>($id);
        if($obj-><?=$id?>) {
            $obj-><?=$id?>-><?=$table?> = $id;
            $obj-><?=$id?>->id = $previous ? $previous->id : 0;
            jzzf_set_<?=$child?>($obj-><?=$id?>);
        } else {
            if($previous) {
                jzzf_delete_<?=$child?>($previous->id);
            }
        }
<? endforeach ?>
        return $id;
    }
<? endif ?>
    return false;
}

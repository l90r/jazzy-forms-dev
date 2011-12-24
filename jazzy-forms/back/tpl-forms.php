<script id="jzzf_tmpl_number" type="text/html">
<li class="jzzf_element">
    <div class="jzzf_element_header">
        <span class="jzzf_type jzzf_number_entry_type">[Number Entry]</span>
        <span class="jzzf_header_title">{{title}}</span>
        <span class="jzzf_toggler">[>]</span>
    </div>
    <div class="jzzf_element_body">
        <fieldset>
            <ul class="jzzf_element_parameters">
                <li>
                    <input type="hidden" class="jzzf_element_type" value="number">
                    <label for="jzzf_element_{{id}}_title">Title</label>
                    <input type="text" id="jzzf_element_{{id}}_title" class="jzzf_element_title" value="{{title}}">
                </li>
                <li>
                    <label for="jzzf_element_{{id}}_name">ID</label>
                    <input type="text" id="jzzf_element_{{id}}_name" class="jzzf_element_name" value="{{name}}">
                </li>
            </ul>
        </fieldset>
        <fieldset>
            <legend>Value</legend>
            <label for="jzzf_element_{{id}}_value">Factor</label>
            <input type="text" id="jzzf_element_{{id}}_value" class="jzzf_element_value" value="{{value}}">
        </fieldset>
    </div>
</li>
</script>
<script type="text/javascript">
    var jzzf_forms = <?php echo json_encode($forms) ?>;
</script>
<h2>Jazzy Forms</h2>
<div id="jzzf_selection">
    <select id="jzzf_selector">
<?php foreach($forms as $form) : ?>
        <option value="<?php echo $form->name ?>"><?php echo htmlspecialchars($form->title) ?></option>
<?php endforeach; ?>
    </select>
    <a id="jzzf_selector_new" href="">New</a> <a id="jzzf_selector_delete" href="">Delete</a>
</div>
<div id="jzzf_new_form">
    <h3>New Form</h3>
    <label for="jzzf_new_form_title">Title</label><input type="text" id="jzzf_new_form_title" name="jzzf_new_form_title" value="New Form">
    <input id="jzzf_new_form_add" type="button" value="Add"><a href="" id="jzzf_new_form_cancel">Cancel</a>
</div>
<div id="message" class="updated" style="display:none"><p></p></div>
<div id="jzzf_form">
    <ul id="jzzf_tabs">
        <li id="jzzf_tab_elements">Elements</li>
        <li id="jzzf_tab_buttons">Buttons</li>
        <li id="jzzf_tab_general">General</li>
    </ul>
    <div id="jzzf_main">
        <div class="jzzf_section" id="jzzf_section_elements">
            <div id="jzzf_elements_toolbox">
                <div id="jzzf_elements_toolbox_description">Click or drag to add</div>
                <ul id="jzzf_elements_toolbox_items">
                    <li id="jzzf_elements_toolbox_number">Number Entry</li>
                    <li id="jzzf_elements_toolbox_dropdown">Dropdown Menu</li>
                    <li id="jzzf_elements_toolbox_radio">Radio Buttons</li>
                    <li id="jzzf_elements_toolbox_checkbox">Checkbox</li>
                    <li id="jzzf_elements_toolbox_output">Output</li>
                    <li id="jzzf_elements_toolbox_hidden">Hidden Field</li>
                </ul>
            </div>
            <div id="jzzf_elements">
                Form Elements
                <ul id="jzzf_elements_list">
                </ul>
            </div>
        </div>
        <div class="jzzf_section" id="jzzf_section_buttons">
            Buttons section (to be implemented)

        </div>
        <div class="jzzf_section" id="jzzf_section_general">           
            General section (to be implemented)
        </div>
    </div>
    <input id="jzzf_form_save" type="button" value="Save" class="button-primary"><a href="" id="jzzf_form_cancel">Discard changes</a>
</div>

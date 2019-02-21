# advanced-goals-editor
Advanced Goals Editor for Pika

This is an alternative editor to the OCM project outcome goals editor. The original editor does not truly allow for "editing" goals. Rather, when one "edits" a goal, the goal is set to inactive in the database and a completely new goal is added. Outcomes associated with the old goal remain associated with the old, now inactivated goal. This behavior is by design and in most cases desirable to prevent organizations from muddying up their data with ill-thought-out edits. 

This alternative editor however, allows for true editing-in-place of existing goals. Please make sure you are fully aware of how the cases, outcomes, and outcome goals tables connect in the database before using this editor. 

This editor should only ever be used for very minor edits of goals that do not change the substantive meaning or definition of a goal. For example, a possible use of this editor could be to edit a goal to expand the acronym SNAP to "Supplemental Nutrition Assistance Program." But you would never ever want to edit a goal to change something like "Obtained SNAP benefits" to "Obtained SSDI benefits." Making edits that change the real substance of an outcome goal beyond very minor changes will result in very messy/corrupt data and an inability to cleanly track historical outcomes over time.
****
Installation of this editor is simple:

1. copy the php file into the base directory of your OCM install (the same directory where your other system screen php files are.)

2. You also should add the following css rules so the fields are wide enough to see what you're doing:
```css
.goaltexteditor {
  width: 56em;
}

.goalordereditor {
  width: 7em;
}

.goalactiveeditor {
  width: 7em;
}
```

3. Within your site_map.html template file, find the line that reads `<li><a href="%%[base_url]%%/system-outcomes.php">Outcome Goals Editor</a></li>` and insert a line below it that reads `<li><a href="%%[base_url]%%/system-outcomes.php">Outcome Goals Editor</a></li>`. Rather than completely replacing the default editor, I recommend leaving both in place and explaining to your staff the difference.
****
For those unfamiliar with the OCM customization and templating system, you should make the changes described above to subtemplates/site_map.html and templates/default.html within your /var/www/html/cms-custom/subtemplates/ directory, not your /var/www/html/cms/subtemplates directory. If there is no cms-custom/subtemplates/ subdirectory and/or site_map.html file within it, create the cms-custom/subtemplates directory and copy the file from cms/subtemplates before beginning to make changes. The same goes for cms-custom/templates/ and cms-custom/templates/default.html. Note also that your OCM may be installed somewhere other than /var/www/html/cms and /var/www/html/cms-custom, but it will always follow the same pattern of there being a \*-custom directory where customizations should be done whenever possible. 

Also, in terms of *where* to add custom css rules, what I do with my clients when it comes to custom css rules is I create a custom css file in cms/css called custom.css and I add all my custom rules there. Then within cms-custom/templates/default.html, I add a line reading `<link href="%%[base_url]%%/css/custom.css" rel="stylesheet">` right after the line that reads `<link href="%%[base_url]%%/css/bootstrap.min.css" rel="stylesheet">`.


If you need help or have questions, please feel free to reach out on the Pika/OCM listserv, or if you are not on it, to me directly alex@metatheria.solutions. 

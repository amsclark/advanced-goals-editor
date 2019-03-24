<?php

/***********************************************/
/* Pika CMS (C) 2015 Pika Software             */
/* http://pikasoftware.com                     */
/* Advanced Goal Editor by Metatheria, LLC 2019*/
/* https://metatheria.solutions                */
/***********************************************/

require_once('pika-danio.php');
pika_init();
require_once('plFlexList.php');
require_once('pikaTempLib.php');
require_once('pikaMenu.php');


$base_url = pl_settings_get('base_url');
$main_html = array();

if (!pika_authorize("system", array()))
{
  $main_html['content'] = "Access denied";
  $main_html['nav'] = "<a href=\"{$base_url}\">Pika Home</a> &gt;
             <a href=\"{$base_url}site_map.php\">Site Map</a> &gt;
             Menus";
  
  $default_template = new pikaTempLib('templates/default.html',$main_html);
  $buffer = $default_template->draw();
  pika_exit($buffer);
}

$action = pl_grab_get('action');
$outcome = pl_grab_get('outcome');
$value = pl_grab_get('value');
$old_value = pl_grab_get('old_value');
$menu_name = pl_grab_get('menu_name');
$field_list = pl_grab_get('field_list');

$numeric_types = array('tinyint','smallint','mediumint','int','bigint',
                'decimal','float','double','real',
                'bit','bool','serial');

$menu_yes_no = pikaTempLib::getMenu('yes_no');
$menu_enable_disable = array('0' => 'Enable', '' => 'Enable', '1' => 'Disable');

switch ($action)
{
  case 'edit':
  
    $outcome = mysql_real_escape_string($outcome);
    $main_html['content'] = "<a href=\"{$base_url}/system-adv_outcomes.php\">Return to Outcome Goals Listing</a>";
    $main_html['content'] .= "<form action=\"{$base_url}/system-adv_outcomes.php?action=update&outcome={$outcome}\" method=\"POST\">";
    $main_html['content'] .= "<table name=\"values\" id=\"values\">";
    $main_html['content'] .= "<th>goal_id</th>";
    $main_html['content'] .= "<th>goal</th>";
    $main_html['content'] .= "<th>outcome_goal_order</th>";
    $main_html['content'] .= "<th>active</th>";
    $sql = "SELECT * FROM outcome_goals WHERE problem ";
    $sql .= " = '{$outcome}' ORDER BY outcome_goal_order ASC";
    $result = mysql_query($sql);
    
    while ($row = mysql_fetch_assoc($result)) 
    {
      $main_html['content'] .= "<tr>\n";
      $main_html['content'] .= "<td>{$row['outcome_goal_id']}<input type=\"hidden\" name=\"outcome_goal_id[{$row['outcome_goal_id']}]\" id=\"outcome_goal_id[{$row['outcome_goal_id']}]\" value=\"{$row['outcome_goal_id']}\"></td>\n";
      $main_html['content'] .= "<td><input class=\"goaltexteditor\" type=\"text\" name=\"goaltext[{$row['outcome_goal_id']}]\" id=\"goaltext[{$row['outcome_goal_id']}]\" value=\"{$row['goal']}\"></td>\n";
      $main_html['content'] .= "<td><input class=\"goalordereditor\" type =\"text\" name=\"goalorder[{$row['outcome_goal_id']}]\" id=\"goalorder[{$row['outcome_goal_id']}]\" value=\"{$row['outcome_goal_order']}\"></td>\n";
      $main_html['content'] .= "<td><input class=\"goalactiveeditor\" type=\"number\" name=\"active[{$row['outcome_goal_id']}]\" id=\"active[{$row['outcome_goal_id']}]\" value=\"{$row['active']}\" min=\"0\" max=\"1\"></td>\n"; 
      $main_html['content'] .= "</tr>\n";
    }
    
    $main_html['content'] .= "</table><br>";
    $main_html['content'] .= "<table name=\"addValues\" id=\"addValues\">";
    $main_html['content'] .= "<th>New Goals</th>";
    $main_html['content'] .= "</table><br>";
    $main_html['content'] .= "<div onclick=\"addrow()\" name=\"addRow\" id=\"addRow\" style=\"background: #ff5f45; width: 30px; height: 30px; border-radius: 15px; text-align: center; color:#fff; \">+</div><br>";
    $main_html['content'] .= "<script>
            var table = document.getElementById(\"addValues\");
            var nextRowNum = table.rows.length;
            function addrow() { 
            row = table.insertRow(nextRowNum); 
            cell = row.insertCell(0);
            cell.innerHTML = \"<input class=\'goaltexteditor\' id=\'newGoal[\" + nextRowNum + \"]\' name=\'newGoal[\" + nextRowNum + \"]\' type=\'text\'>\";
            nextRowNum++;
            }
            </script>";
    $main_html['content'] .= "<input type=\"submit\">\n";
    $main_html['nav'] = "<a href=\"{$base_url}\">Pika Home</a> &gt;
               <a href=\"{$base_url}/site_map.php\">Site Map</a> &gt;
               <a href=\"{$base_url}/system-menus.php\">Menus</a> &gt;
               Editing {$menu_name}";
    break;
    
  case 'update':
    $outcome = mysql_real_escape_string(pl_grab_get('outcome'));
    $goalOrderMax = max($_POST['goalorder']);
    $cleanGoalOrderMax = mysql_real_escape_string($goalOrderMax) + 1;
                echo "<pre>";
    $sqlStatements = array();
    foreach ($_POST['goaltext'] as $key => $val)
    {
      //clean $key and $val first before forming statement
      $cleanKey = mysql_real_escape_string($key);
      $cleanVal = mysql_real_escape_string($val);
      $nextStatement = "UPDATE outcome_goals set goal=\"$cleanVal\" where outcome_goal_id=\"$cleanKey\"";
                        array_push($sqlStatements, $nextStatement);
    }
    foreach ($_POST['active'] as $key => $val)
    {
      //clean $key and $val first before forming statement
      $cleanKey = mysql_real_escape_string($key);
      $cleanVal = mysql_real_escape_string($val);
      $nextStatement = "UPDATE outcome_goals set active=\"$cleanVal\" where outcome_goal_id=\"$cleanKey\"";
      array_push($sqlStatements, $nextStatement);
    }
    foreach ($_POST['goalorder'] as $key => $val)
    {
      //clean $key and $val first before forming statement
      $cleanKey = mysql_real_escape_string($key);
      $cleanVal = mysql_real_escape_string($val);
      if ($cleanVal == ""){
      $nextStatement = "UPDATE outcome_goals set outcome_goal_order=NULL where outcome_goal_id=\"$cleanKey\"";
      }
      else {
      $nextStatement = "UPDATE outcome_goals set outcome_goal_order=\"$cleanVal\" where outcome_goal_id=\"$cleanKey\"";
      }
      array_push($sqlStatements, $nextStatement);
    }

    //array of sql statements has been assembled in an array. Let's execute them.
    foreach ($sqlStatements as $sqlExec)
    {
      echo $sqlExec . "\n";
      mysql_query($sqlExec) or trigger_error("SQL: " . $sql . " ERROR: " . mysql_error());
      
    }
    echo "\ncontents of _POST['newGoal']\n\n";
    print_r($_POST['newGoal']);
          echo "</pre>";
    
                require_once('pikaOutcomeGoal.php');
        
    foreach($_POST['newGoal'] as $goal)
                {
                        $goal = trim($goal);
                        
                        if (strlen($goal) > 0)
                        {
                                
                                
                                $g = new pikaOutcomeGoal();
                                $g->goal = $goal;
                                $g->problem = $outcome;
                                $g->active = 1;
                                $g->outcome_goal_order = $cleanGoalOrderMax++;
                                $g->save();
                        }
                }


    header("Location: {$base_url}/system-adv_outcomes.php?action=edit&outcome={$outcome}");
    exit();   
    break;
    
  default:
  
    $main_html['content'] .= "<h2>Please select a Problem Code category to edit</h2><div class=\"span4\">";
    
    for ($i = 0; $i < 10; $i++)
    {
      $main_html['content'] .= "<a class=\"btn btn-block\" href=\"{$base_url}/system-adv_outcomes.php?action=edit&outcome={$i}X\">{$i}0's</a><br>\n";
    }
    
    $problem_codes = pl_menu_get('problem_2008');
    
    foreach ($problem_codes as $key => $value)
    {
      $key = str_pad($key, 2, "0", STR_PAD_LEFT);
      $main_html['content'] .= "<a class=\"btn btn-block\" href=\"{$base_url}/system-adv_outcomes.php?action=edit&outcome={$key}\">{$value}</a><br>\n";
    }

    $main_html['content'] .= "</div>";
    
    $main_html['nav'] = "<a href=\"{$base_url}\">Pika Home</a> &gt;
               <a href=\"{$base_url}/site_map.php\">Site Map</a> &gt;
               Menus";
    
    break;
}

$main_html['page_title'] = 'Advanced Outcomes Editor';
$default_template = new pikaTempLib('templates/default.html',$main_html);
$buffer = $default_template->draw();
pika_exit($buffer);

?>

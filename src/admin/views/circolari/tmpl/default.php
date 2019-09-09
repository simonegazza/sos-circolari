<?php
defined('_JEXEC') or die('Restricted Access');

use Joomla\CMS\Factory;
$input = Factory::getApplication()->input;

$requestYear = $_REQUEST["filter_anno"];
$requestGroup = $_REQUEST ["filter_group"];
?>
<form action="index.php?option=com_sos_circolari&view=circolari" method="post" id="adminForm" name="adminForm">
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="task" value=""/>
    <input type="hidden" name="task" value=""/>
    <label class="filter-search-lbl" for="filter_search">Filtro: </label>
    <input class="input_search" style="height: 100%;margin-bottom: initial;" type="text" name="filter_search" id="filter_search" value="<?php echo $input->get("filter_search"); ?>" />
    <button type="submit" class="btn">Cerca</button>
    <button type="button" class="btn" onclick="document.getElementById('filter_search').value='';this.form.submit();">Pulisci</button>
    <select id="filter_anno" name="filter_anno" class="btn hasTooltip js-stools-btn-filter table_filter" onchange="this.form.submit()" >
        <option value="">Tutti Gli Anni</option>
        <?php foreach (getAcademicYearsInDB() as $year) {
             if ($year == getAcademicYear() && !isset($_REQUEST["filter_anno"])) { $selected = "selected=\"selected\""; }
            elseif ($year == $requestYear) { $selected = "selected=\"selected\""; }
            else { $selected = ""; } ?>
            <option value="<?php echo $year; ?>" <?php echo $selected ?> > <?php echo $year; ?> </option>
        <?php } ?>
    </select>
    <select name="filter_group" class="btn hasTooltip js-stools-btn-filter" onchange="this.form.submit()" >
        <option value="">Tutti i Gruppi</option>
        <?php foreach (getRecipientGroupsInDB() as $group) {
            if ($requestGroup == $group) { $selected = "selected=\"selected\""; }
            else { $selected = ""; } ?>
            <option value="<?php echo $group; ?>" <?php echo $selected?> > <?php echo $group; ?> </option>
        <?php } ?>
    </select>
    <table class="table table-striped table-hover">
        <thead>
        <tr>
            <th>#</th>
            <th>
                <?php echo JHtml::_('grid.checkall'); ?>
            </th>
            <th>Oggetto</th>
            <th>Autore</th>
            <th>Anno scolastico</th>
        </tr>
        </thead>
        <tbody>
        <?php if (!empty($this->items)) : ?>
            <?php foreach ($this->items as $i => $row) : ?>
                <tr>
                    <td><?php echo $row->numero; ?></td>
                    <td align="center">
                        <?php echo JHtml::_('grid.id', $i, $row->id); ?>
                    </td>
                    <td>
                        <a href="<?php echo JRoute::_('index.php?option=com_sos_circolari&view=circolare&id=' . $row->id); ?>">
                            <?php echo $row->oggetto; ?>
                        </a>
                    </td>
                    <td><?php echo $row->name; ?></td>
                    <td><?php echo $row->anno_scolastico; ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
    <input type="hidden" name="boxchecked" value="0"/>
    <?php echo JHtml::_('form.token'); ?>
</form>


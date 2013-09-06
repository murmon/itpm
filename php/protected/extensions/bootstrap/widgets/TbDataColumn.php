<?php
/**
 * TbDataColumn class file.
 * @author Christoffer Niska <ChristofferNiska@gmail.com>
 * @copyright Copyright &copy; Christoffer Niska 2011-
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package bootstrap.widgets
 */

Yii::import('zii.widgets.grid.CDataColumn');

/**
 * Bootstrap grid data column.
 */
class TbDataColumn extends CDataColumn
{
    /**
     * Renders the header cell content.
     * This method will render a link that can trigger the sorting if the column is sortable.
     */
    public $evaluateHtmlOptions = false;

    protected function renderHeaderCellContent()
    {
        if ($this->grid->enableSorting && $this->sortable && $this->name !== null) {
            $sort = $this->grid->dataProvider->getSort();
            $label = isset($this->header) ? $this->header : $sort->resolveLabel($this->name);

            if ($sort->resolveAttribute($this->name) !== false)
                $label .= '<span class="caret"></span>';

            echo $sort->link($this->name, $label, array('class' => 'sort-link'));
        } else {
            if ($this->name !== null && $this->header === null) {
                if ($this->grid->dataProvider instanceof CActiveDataProvider)
                    echo CHtml::encode($this->grid->dataProvider->model->getAttributeLabel($this->name));
                else
                    echo CHtml::encode($this->name);
            } else
                parent::renderHeaderCellContent();
        }
    }

    /**
     * Renders the filter cell.
     */
    public function renderFilterCell()
    {
        echo '<td><div class="filter-container">';
        $this->renderFilterCellContent();
        echo '</div></td>';
    }

    public function renderDataCell($row)
    {
        $data = $this->grid->dataProvider->data[$row];
        if ($this->evaluateHtmlOptions) {
            foreach ($this->htmlOptions as $key => $value) {
                $options[$key] = $this->evaluateExpression($value, array('row' => $row, 'data' => $data));
            }
        } else $options = $this->htmlOptions;
        if ($this->cssClassExpression !== null) {
            $class = $this->evaluateExpression($this->cssClassExpression, array('row' => $row, 'data' => $data));
            if (isset($options['class']))
                $options['class'] .= ' ' . $class;
            else
                $options['class'] = $class;
        }
        echo CHtml::openTag('td', $options);
        $this->renderDataCellContent($row, $data);
        echo '</td>';
    }


}

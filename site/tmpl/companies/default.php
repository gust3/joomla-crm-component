<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
?>

<div class="crm-dashboard">
    <h1>Мои компании</h1>

    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
            <tr>
                <th>ID</th>
                <th>Название</th>
                <th>Стадия</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($this->items as $item): ?>
                <tr>
                    <td><?php echo (int)$item->id; ?></td>
                    <td><?php echo $this->escape($item->name); ?></td>
                    <td>
                        <span class="badge bg-primary">
                            <?php echo $this->escape($item->current_stage); ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?php echo Route::_('index.php?option=com_crmstages&view=company&id=' . (int)$item->id); ?>"
                           class="btn btn-sm btn-success">
                            Открыть карточку
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- 🔹 Блок кнопок демо-данных -->
    <div class="mb-3 p-3 bg-light rounded border">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <strong>🧪 Тестовые данные</strong>
                <p class="text-muted mb-0 small">
                    📌 Компании создаются со <strong>случайной стадией</strong>
                    (Ice, Touched, Aware, Interested, demo_planned, Demo_done, Committed)
                    <br>
                    💡 Удобно для тестирования переходов между этапами воронки
                </p>
            </div>
            <div class="btn-group" role="group">

                <!-- +1 Компания -->
                <form action="<?php echo Route::_('index.php?option=com_crmstages&task=companies.addDemoCompany'); ?>"
                      method="post" style="display:inline;">
                    <?php echo HTMLHelper::_('form.token'); ?>
                    <button type="submit" class="btn btn-sm btn-outline-primary">
                        🎲 +1 Компания
                    </button>
                </form>

                <!-- +5 Компаний -->
                <form action="<?php echo Route::_('index.php?option=com_crmstages&task=companies.addMultipleDemoCompanies'); ?>"
                      method="post" style="display:inline;">
                    <?php echo HTMLHelper::_('form.token'); ?>
                    <input type="hidden" name="count" value="5">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        🎲 +5 Компаний
                    </button>
                </form>

                <!-- +10 Компаний -->
                <form action="<?php echo Route::_('index.php?option=com_crmstages&task=companies.addMultipleDemoCompanies'); ?>"
                      method="post" style="display:inline;">
                    <?php echo HTMLHelper::_('form.token'); ?>
                    <input type="hidden" name="count" value="10">
                    <button type="submit" class="btn btn-sm btn-outline-secondary">
                        🎲 +10 Компаний
                    </button>
                </form>

                <!-- Очистить демо -->
                <form action="<?php echo Route::_('index.php?option=com_crmstages&task=companies.clearDemoCompanies'); ?>"
                      method="post" style="display:inline;"
                      onsubmit="return confirm('Удалить все демо-компании (ООО/АО/ИП)?');">
                    <?php echo HTMLHelper::_('form.token'); ?>
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        🗑️ Очистить демо
                    </button>
                </form>

            </div>
        </div>
    </div>
</div>
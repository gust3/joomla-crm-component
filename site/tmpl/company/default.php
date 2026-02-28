<?php
defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Gust\Component\Crmstages\Site\Service\StageFactory;

if (!$this->item) {
    echo '<div class="alert alert-error">Компания не найдена!</div>';
    return;
}
?>

<div class="card mb-4">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <div>
            <h4><?php echo $this->escape($this->item->name); ?></h4>
            <span class="badge bg-warning text-dark">
                Стадия: <?php echo $this->escape($this->item->current_stage); ?>
            </span>
        </div>
        <div>
            <span class="badge bg-info text-dark">
                MLS: <?php echo $this->escape($this->mlsCode ?? 'N/A'); ?>
            </span>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($this->item->current_stage)): ?>
            <div class="alert alert-info mb-3">
                <strong>Описание:</strong>
                <?php
                try {
                    $stage = StageFactory::create($this->item->current_stage);
                    echo $stage->getDescription();
                } catch (\Exception $e) {
                    echo 'Ошибка: ' . $e->getMessage();
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <h5>Доступные действия</h5>

                <?php if (empty($this->availableActions)): ?>
                    <div class="alert alert-info">Нет доступных действий на этой стадии.</div>
                <?php else: ?>
                    <?php foreach ($this->availableActions as $action): ?>
                        <?php
                        if (empty($action['code']) || empty($action['label'])) {
                            continue;
                        }
                        $showFields = !empty($action['fields']);
                        ?>

                        <div class="card mb-2">
                            <div class="card-body">
                                <button type="button" class="btn btn-outline-primary w-100"
                                        onclick="document.getElementById('action-<?php echo $action['code']; ?>').classList.toggle('d-none')">
                                    <?php echo $this->escape($action['label']); ?>
                                </button>

                                <div id="action-<?php echo $action['code']; ?>" class="d-none mt-3">
                                    <form method="post" action="<?php echo Route::_('index.php?option=com_crmstages&task=company.performAction', false); ?>">
                                        <input type="hidden" name="id" value="<?php echo (int) $this->item->id; ?>">
                                        <input type="hidden" name="action" value="<?php echo $this->escape($action['code']); ?>">
                                        <?php echo HTMLHelper::_('form.token'); ?>

                                        <?php if ($showFields): ?>
                                            <?php foreach ($action['fields'] as $fieldName => $fieldConfig): ?>
                                                <div class="mb-3">
                                                    <label class="form-label"><?php echo $this->escape($fieldConfig['label']); ?></label>
                                                    <?php if ($fieldConfig['type'] === 'textarea'): ?>
                                                        <textarea name="<?php echo $this->escape($fieldName); ?>" class="form-control" rows="2" <?php echo $fieldConfig['required'] ? 'required' : ''; ?>></textarea>
                                                    <?php elseif ($fieldConfig['type'] === 'select'): ?>
                                                        <select name="<?php echo $this->escape($fieldName); ?>" class="form-select" <?php echo $fieldConfig['required'] ? 'required' : ''; ?>>
                                                            <option value="">Выберите...</option>
                                                            <option value="0">❌ Нет</option>
                                                            <option value="1">✅ Да</option>
                                                        </select>
                                                    <?php else: ?>
                                                        <input type="<?php echo $this->escape($fieldConfig['type']); ?>" name="<?php echo $this->escape($fieldName); ?>" class="form-control" <?php echo $fieldConfig['required'] ? 'required' : ''; ?>>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php endif; ?>

                                        <button type="submit" class="btn btn-success w-100">✓ Выполнить</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="col-md-6">
                <h5>Инструкция</h5>
                <div class="alert alert-secondary">
                    Следуйте регламенту для текущей стадии.
                </div>
            </div>
        </div>

        <hr>

        <?php if (!empty($this->discoveryData)): ?>
            <div class="card mb-3 border-success">
                <div class="card-header bg-success text-white">
                    ✅ Discovery заполнен
                    <small class="float-end">
                        <?php echo !empty($this->discoveryData['filled_at'])
                                ? HTMLHelper::_('date', $this->discoveryData['filled_at'], 'd.m.Y H:i')
                                : ''; ?>
                    </small>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <?php if (!empty($this->discoveryData['pains'])): ?>
                            <dt class="col-sm-4">🎯 Основные проблемы / задачи</dt>
                            <dd class="col-sm-8"><?php echo nl2br($this->escape($this->discoveryData['pains'])); ?></dd>
                        <?php endif; ?>

                        <?php if (!empty($this->discoveryData['budget'])): ?>
                            <dt class="col-sm-4">💰 Бюджет</dt>
                            <dd class="col-sm-8"><?php echo $this->escape($this->discoveryData['budget']); ?></dd>
                        <?php endif; ?>

                        <?php if (!empty($this->discoveryData['timeline'])): ?>
                            <dt class="col-sm-4">📅 Сроки</dt>
                            <dd class="col-sm-8"><?php echo $this->escape($this->discoveryData['timeline']); ?></dd>
                        <?php endif; ?>

                        <?php if (!empty($this->discoveryData['decision_maker'])): ?>
                            <dt class="col-sm-4">👥 ЛПР</dt>
                            <dd class="col-sm-8"><?php echo $this->escape($this->discoveryData['decision_maker']); ?></dd>
                        <?php endif; ?>

                        <?php if (!empty($this->discoveryData['next_steps'])): ?>
                            <dt class="col-sm-4">🔄 Следующие шаги</dt>
                            <dd class="col-sm-8"><?php echo nl2br($this->escape($this->discoveryData['next_steps'])); ?></dd>
                        <?php endif; ?>
                    </dl>
                </div>
            </div>
        <?php endif; ?>

        <hr>


        <h5>История событий</h5>
        <ul class="list-group">
            <?php if (empty($this->history)): ?>
                <li class="list-group-item text-muted">История пуста</li>
            <?php else: ?>
                <?php foreach ($this->history as $event): ?>
                    <li class="list-group-item">
                        <strong><?php echo $this->escape($event->event_code); ?></strong>
                        <small class="text-muted">(<?php echo $event->created; ?>)</small>
                        <?php if (!empty($event->comment)): ?>
                            <p class="mb-0 text-muted"><?php echo $this->escape($event->comment); ?></p>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

<a href="<?php echo Route::_('index.php?option=com_crmstages&view=companies', false); ?>" class="btn btn-secondary">← Назад</a>
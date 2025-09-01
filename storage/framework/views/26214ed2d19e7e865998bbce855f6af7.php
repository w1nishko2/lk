

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><?php echo e($block->name); ?></h2>
                <div>
                    <a href="<?php echo e(route('blocks.preview', $block)); ?>" class="btn btn-info" target="_blank">
                        <i class="fas fa-external-link-alt"></i> Предпросмотр
                    </a>
                    <a href="<?php echo e(route('blocks.edit', $block)); ?>" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Редактировать
                    </a>
                    <a href="<?php echo e(route('blocks.index')); ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Назад к списку
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-8">
                    <!-- Основная информация -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Информация о блоке</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Название:</label>
                                        <div><?php echo e($block->name); ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Тип:</label>
                                        <div><?php echo e($block->type); ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Категория:</label>
                                        <span class="badge bg-primary"><?php echo e($block->category); ?></span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Статус:</label>
                                        <div>
                                            <span class="badge bg-<?php echo e($block->is_active ? 'success' : 'secondary'); ?>">
                                                <?php echo e($block->is_active ? 'Активен' : 'Неактивен'); ?>

                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Порядок сортировки:</label>
                                        <div><?php echo e($block->sort_order); ?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Создан:</label>
                                        <div><?php echo e($block->created_at->format('d.m.Y H:i')); ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php if($block->description): ?>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Описание:</label>
                                    <div class="text-muted"><?php echo e($block->description); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- HTML контент -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">HTML контент</h5>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('html_code')">
                                <i class="fas fa-copy"></i> Копировать
                            </button>
                        </div>
                        <div class="card-body">
                            <pre class="bg-light p-3 rounded"><code id="html_code"><?php echo e($block->html_content); ?></code></pre>
                        </div>
                    </div>

                    <?php if($block->css_content): ?>
                    <!-- CSS контент -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">CSS стили</h5>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('css_code')">
                                <i class="fas fa-copy"></i> Копировать
                            </button>
                        </div>
                        <div class="card-body">
                            <pre class="bg-light p-3 rounded"><code id="css_code"><?php echo e($block->css_content); ?></code></pre>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if($block->js_content): ?>
                    <!-- JavaScript контент -->
                    <div class="card mt-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">JavaScript код</h5>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyToClipboard('js_code')">
                                <i class="fas fa-copy"></i> Копировать
                            </button>
                        </div>
                        <div class="card-body">
                            <pre class="bg-light p-3 rounded"><code id="js_code"><?php echo e($block->js_content); ?></code></pre>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-4">
                    <!-- Действия -->
                    <div class="card sticky-top">
                        <div class="card-header">
                            <h5 class="mb-0">Действия</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="<?php echo e(route('blocks.preview', $block)); ?>" class="btn btn-info" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Открыть предпросмотр
                                </a>
                                <a href="<?php echo e(route('blocks.edit', $block)); ?>" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Редактировать
                                </a>
                                <button type="button" class="btn btn-outline-secondary" onclick="duplicateBlock()">
                                    <i class="fas fa-copy"></i> Дублировать
                                </button>
                                <hr>
                                <form method="POST" action="<?php echo e(route('blocks.destroy', $block)); ?>" onsubmit="return confirm('Вы уверены, что хотите удалить этот блок?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash"></i> Удалить блок
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="text-muted small">
                                <div><strong>ID:</strong> <?php echo e($block->id); ?></div>
                                <div><strong>Создан:</strong> <?php echo e($block->created_at->format('d.m.Y H:i')); ?></div>
                                <div><strong>Обновлён:</strong> <?php echo e($block->updated_at->format('d.m.Y H:i')); ?></div>
                            </div>
                        </div>
                    </div>

                    <!-- Предварительный просмотр -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <h5 class="mb-0">Встроенный предпросмотр</h5>
                        </div>
                        <div class="card-body p-0">
                            <iframe 
                                src="<?php echo e(route('blocks.preview', $block)); ?>" 
                                style="width: 100%; height: 300px; border: none; border-radius: 0 0 0.375rem 0.375rem;"
                                title="Предпросмотр блока">
                            </iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent;
    
    navigator.clipboard.writeText(text).then(function() {
        // Показать уведомление об успешном копировании
        const button = event.target.closest('button');
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> Скопировано!';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');
        
        setTimeout(function() {
            button.innerHTML = originalText;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    }).catch(function(err) {
        console.error('Ошибка при копировании: ', err);
        alert('Не удалось скопировать в буфер обмена');
    });
}

function duplicateBlock() {
    // Перенаправляем на страницу создания с предзаполненными данными
    const createUrl = '<?php echo e(route("blocks.create")); ?>';
    const params = new URLSearchParams({
        duplicate_from: '<?php echo e($block->id); ?>',
        name: '<?php echo e($block->name); ?> (копия)',
        type: '<?php echo e($block->type); ?>',
        category: '<?php echo e($block->category); ?>',
        description: '<?php echo e($block->description); ?>',
        html_content: `<?php echo e(addslashes($block->html_content)); ?>`,
        css_content: `<?php echo e(addslashes($block->css_content)); ?>`,
        js_content: `<?php echo e(addslashes($block->js_content)); ?>`,
        sort_order: '<?php echo e($block->sort_order); ?>',
        is_active: '<?php echo e($block->is_active ? 1 : 0); ?>'
    });
    
    window.location.href = createUrl + '?' + params.toString();
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\konstructor\resources\views\blocks\show.blade.php ENDPATH**/ ?>
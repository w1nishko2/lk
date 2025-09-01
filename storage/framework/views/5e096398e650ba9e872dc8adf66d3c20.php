

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Созданные сайты</h2>
                <a href="<?php echo e(route('site-builder.index')); ?>" class="btn btn-primary">
                    Создать новый сайт
                </a>
            </div>

            <?php if($sites->count() > 0): ?>
                <div class="row">
                    <?php $__currentLoopData = $sites; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $site): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><?php echo e($site->name); ?></h6>
                                <span class="badge bg-<?php echo e($site->status === 'published' ? 'success' : 'secondary'); ?>">
                                    <?php echo e(ucfirst($site->status)); ?>

                                </span>
                            </div>
                            <div class="card-body">
                                <p class="card-text">
                                    <strong>Шаблон:</strong> <?php echo e($site->template->name); ?><br>
                                    <strong>Блоков:</strong> <?php echo e(count($site->selected_blocks)); ?><br>
                                    <?php if($site->domain): ?>
                                    <strong>Домен:</strong> <?php echo e($site->domain); ?><br>
                                    <?php endif; ?>
                                    <strong>Создан:</strong> <?php echo e($site->created_at->format('d.m.Y H:i')); ?>

                                </p>
                                
                                <div class="mb-3">
                                    <small class="text-muted">Использованные блоки:</small>
                                    <div class="mt-1">
                                        <?php $__currentLoopData = $site->getSelectedBlocksModels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <span class="badge bg-light text-dark me-1"><?php echo e($block->name); ?></span>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-grid gap-2">
                                    <a href="<?php echo e(route('site-builder.download', $site->id)); ?>" 
                                       class="btn btn-primary btn-sm">
                                        <i class="fas fa-download"></i> Скачать
                                    </a>
                                    <?php if(file_exists(public_path($site->folder_path . '/index.html'))): ?>
                                    <a href="<?php echo e(asset($site->folder_path . '/index.html')); ?>" 
                                       target="_blank" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-eye"></i> Просмотр
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Статистика</h5>
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <h4 class="text-primary"><?php echo e($sites->count()); ?></h4>
                                        <p class="mb-0">Всего сайтов</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="text-success"><?php echo e($sites->where('status', 'published')->count()); ?></h4>
                                        <p class="mb-0">Опубликованных</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="text-warning"><?php echo e($sites->where('status', 'draft')->count()); ?></h4>
                                        <p class="mb-0">Черновиков</p>
                                    </div>
                                    <div class="col-md-3">
                                        <h4 class="text-info"><?php echo e($sites->where('created_at', '>=', now()->subWeek())->count()); ?></h4>
                                        <p class="mb-0">За неделю</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-globe fa-4x text-muted mb-3"></i>
                    <h4>Пока нет созданных сайтов</h4>
                    <p class="text-muted">Создайте свой первый сайт с помощью конструктора</p>
                    <a href="<?php echo e(route('site-builder.index')); ?>" class="btn btn-primary">
                        Создать сайт
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\konstructor\resources\views\site-builder\sites.blade.php ENDPATH**/ ?>
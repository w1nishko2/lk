

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Управление блоками</h2>
                <a href="<?php echo e(route('blocks.create')); ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Создать блок
                </a>
            </div>

            <?php if(session('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('success')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Фильтр по категориям -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="categoryFilter" class="form-label">Фильтр по категории:</label>
                            <select id="categoryFilter" class="form-select">
                                <option value="">Все категории</option>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category); ?>"><?php echo e($category); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="searchFilter" class="form-label">Поиск по названию:</label>
                            <input type="text" id="searchFilter" class="form-control" placeholder="Введите название блока...">
                        </div>
                    </div>
                </div>
            </div>

            <?php if($blocks->count() > 0): ?>
                <div class="row" id="blocksContainer">
                    <?php $__currentLoopData = $blocks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $block): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 col-lg-4 mb-4 block-card" data-category="<?php echo e($block->category); ?>" data-name="<?php echo e(strtolower($block->name)); ?>">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><?php echo e($block->name); ?></h6>
                                <span class="badge bg-<?php echo e($block->is_active ? 'success' : 'secondary'); ?>">
                                    <?php echo e($block->is_active ? 'Активен' : 'Неактивен'); ?>

                                </span>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-2">
                                    <strong>Категория:</strong> <?php echo e($block->category); ?>

                                </p>
                                <p class="text-muted small mb-2">
                                    <strong>Тип:</strong> <?php echo e($block->type); ?>

                                </p>
                                <?php if($block->description): ?>
                                    <p class="card-text small"><?php echo e(Str::limit($block->description, 100)); ?></p>
                                <?php endif; ?>
                                <div class="text-muted small">
                                    <i class="fas fa-calendar-alt"></i> <?php echo e($block->created_at->format('d.m.Y H:i')); ?>

                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="btn-group w-100" role="group">
                                    <a href="<?php echo e(route('blocks.show', $block)); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('blocks.preview', $block)); ?>" class="btn btn-sm btn-outline-info" target="_blank">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                    <a href="<?php echo e(route('blocks.edit', $block)); ?>" class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="<?php echo e(route('blocks.destroy', $block)); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Вы уверены, что хотите удалить этот блок?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <!-- Пагинация -->
                <div class="d-flex justify-content-center mt-4">
                    <?php echo e($blocks->links()); ?>

                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-cubes fa-4x text-muted mb-3"></i>
                    <h4>Блоки не найдены</h4>
                    <p class="text-muted">Создайте свой первый блок для конструктора</p>
                    <a href="<?php echo e(route('blocks.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Создать блок
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const categoryFilter = document.getElementById('categoryFilter');
    const searchFilter = document.getElementById('searchFilter');
    const blockCards = document.querySelectorAll('.block-card');

    function filterBlocks() {
        const selectedCategory = categoryFilter.value.toLowerCase();
        const searchTerm = searchFilter.value.toLowerCase();

        blockCards.forEach(card => {
            const category = card.dataset.category.toLowerCase();
            const name = card.dataset.name;
            
            const categoryMatch = !selectedCategory || category === selectedCategory;
            const nameMatch = !searchTerm || name.includes(searchTerm);
            
            if (categoryMatch && nameMatch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    categoryFilter.addEventListener('change', filterBlocks);
    searchFilter.addEventListener('input', filterBlocks);
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\OSPanel\domains\konstructor\resources\views\blocks\index.blade.php ENDPATH**/ ?>
<?php if (isset($component)) { $__componentOriginal4619374cef299e94fd7263111d0abc69 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4619374cef299e94fd7263111d0abc69 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.app-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('График смен')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Calendar View -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Календарь смен</h3>
                        <div id="calendar" class="calendar-container"></div>
                    </div>

                    <!-- First Period Table -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Смены с 30 по 14</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Дата</th>
                                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Начало</th>
                                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Конец</th>
                                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Тип</th>
                                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Примечания</th>
                                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $firstPeriodShifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200"><?php echo e($shift->date->format('d.m.Y')); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200"><?php echo e($shift->start_time->format('H:i')); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200"><?php echo e($shift->end_time->format('H:i')); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200"><?php echo e($shift->type); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200"><?php echo e($shift->notes); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                                            <button onclick="editShift(<?php echo e($shift->id); ?>)" class="text-indigo-600 hover:text-indigo-900 mr-3">Редактировать</button>
                                            <form action="<?php echo e(route('shifts.destroy', $shift)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="text-red-600 hover:text-red-900">Удалить</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Second Period Table -->
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold mb-4">Смены с 15 по 29</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Дата</th>
                                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Начало</th>
                                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Конец</th>
                                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Тип</th>
                                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Примечания</th>
                                        <th class="px-6 py-3 border-b-2 border-gray-200 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__currentLoopData = $secondPeriodShifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200"><?php echo e($shift->date->format('d.m.Y')); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200"><?php echo e($shift->start_time->format('H:i')); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200"><?php echo e($shift->end_time->format('H:i')); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200"><?php echo e($shift->type); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200"><?php echo e($shift->notes); ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap border-b border-gray-200">
                                            <button onclick="editShift(<?php echo e($shift->id); ?>)" class="text-indigo-600 hover:text-indigo-900 mr-3">Редактировать</button>
                                            <form action="<?php echo e(route('shifts.destroy', $shift)); ?>" method="POST" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="text-red-600 hover:text-red-900">Удалить</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Add Shift Button -->
                    <div class="mt-4">
                        <button onclick="showAddShiftModal()" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Добавить смену
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add/Edit Shift Modal -->
    <div id="shiftModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900" id="modalTitle">Добавить смену</h3>
                <form id="shiftForm" class="mt-4" action="javascript:void(0);" method="post">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="shiftId" name="shift_id">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="date">
                            Дата
                        </label>
                        <input type="date" id="date" name="date" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="start_time">
                            Время начала
                        </label>
                        <input type="time" id="start_time" name="start_time" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="end_time">
                            Время окончания
                        </label>
                        <input type="time" id="end_time" name="end_time" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="type">
                            Тип смены
                        </label>
                        <select id="type" name="type" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="regular">Обычная</option>
                            <option value="overtime">Сверхурочная</option>
                            <option value="night">Ночная</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="notes">
                            Примечания
                        </label>
                        <textarea id="notes" name="notes"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" onclick="hideShiftModal()"
                            class="bg-gray-500 text-white px-4 py-2 rounded mr-2">Отмена</button>
                        <button type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'ru',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: [
                    <?php $__currentLoopData = $firstPeriodShifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    {
                        id: '<?php echo e($shift->id); ?>',
                        title: 'Смена',
                        start: '<?php echo e($shift->date->format('Y-m-d')); ?>T<?php echo e($shift->start_time->format('H:i:s')); ?>',
                        end: '<?php echo e($shift->date->format('Y-m-d')); ?>T<?php echo e($shift->end_time->format('H:i:s')); ?>',
                        color: '<?php echo e($shift->type === 'regular' ? '#4CAF50' : ($shift->type === 'overtime' ? '#FFC107' : '#2196F3')); ?>'
                    },
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php $__currentLoopData = $secondPeriodShifts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $shift): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    {
                        id: '<?php echo e($shift->id); ?>',
                        title: 'Смена',
                        start: '<?php echo e($shift->date->format('Y-m-d')); ?>T<?php echo e($shift->start_time->format('H:i:s')); ?>',
                        end: '<?php echo e($shift->date->format('Y-m-d')); ?>T<?php echo e($shift->end_time->format('H:i:s')); ?>',
                        color: '<?php echo e($shift->type === 'regular' ? '#4CAF50' : ($shift->type === 'overtime' ? '#FFC107' : '#2196F3')); ?>'
                    },
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                ],
                eventClick: function(info) {
                    editShift(info.event.id);
                }
            });
            calendar.render();
        });

        function showAddShiftModal() {
            document.getElementById('modalTitle').textContent = 'Добавить смену';
            document.getElementById('shiftForm').reset();
            document.getElementById('shiftId').value = '';
            document.getElementById('shiftModal').classList.remove('hidden');
        }

        function hideShiftModal() {
            document.getElementById('shiftModal').classList.add('hidden');
        }

        function editShift(shiftId) {
            document.getElementById('modalTitle').textContent = 'Редактировать смену';
            fetch(`/shifts/${shiftId}`)
                .then(response => {
                    const contentType = response.headers.get('content-type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json();
                    } else {
                        throw new Error('Сервер вернул не JSON. Возможно, вы не авторизованы или произошла ошибка.');
                    }
                })
                .then(data => {
                    document.getElementById('shiftId').value = data.id;
                    document.getElementById('date').value = data.date;
                    document.getElementById('start_time').value = data.start_time;
                    document.getElementById('end_time').value = data.end_time;
                    document.getElementById('type').value = data.type;
                    document.getElementById('notes').value = data.notes || '';
                    document.getElementById('shiftModal').classList.remove('hidden');
                })
                .catch(error => {
                    alert(error.message);
                    console.error(error);
                });
        }

        document.getElementById('shiftForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const shiftId = formData.get('shift_id');
            const url = shiftId ? `/shifts/${shiftId}` : '/shifts';
            const method = shiftId ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text().then(text => {
                    console.log('Response text:', text.substring(0, 200));
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Сервер вернул не JSON. Вот ответ: ' + text.substring(0, 200));
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else if (data.errors) {
                    let errorMsg = 'Ошибки валидации:\n';
                    for (const key in data.errors) {
                        errorMsg += data.errors[key].join(' ') + '\n';
                    }
                    alert(errorMsg);
                } else {
                    alert('Ошибка при сохранении смены');
                }
            })
            .catch(error => {
                alert(error.message);
                console.error('Error:', error);
            });
            return false;
        });
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4619374cef299e94fd7263111d0abc69)): ?>
<?php $attributes = $__attributesOriginal4619374cef299e94fd7263111d0abc69; ?>
<?php unset($__attributesOriginal4619374cef299e94fd7263111d0abc69); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4619374cef299e94fd7263111d0abc69)): ?>
<?php $component = $__componentOriginal4619374cef299e94fd7263111d0abc69; ?>
<?php unset($__componentOriginal4619374cef299e94fd7263111d0abc69); ?>
<?php endif; ?> <?php /**PATH C:\Users\graza\Desktop\Новая папка (2)\finance-app\resources\views/shifts/index.blade.php ENDPATH**/ ?>
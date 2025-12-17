import axios from 'axios';

const { createElement: h, useEffect, useMemo, useState } = window.React;
const { createRoot } = window.ReactDOM;

const api = axios.create({
    baseURL: '/api/crm',
    headers: {
        Accept: 'application/json',
    },
});

const toneMap = {
    green: 'bg-green-100 text-green-800',
    amber: 'bg-amber-100 text-amber-800',
    slate: 'bg-slate-100 text-slate-800',
    blue: 'bg-blue-100 text-blue-800',
    rose: 'bg-rose-100 text-rose-800',
};

const StatusPill = ({ tone = 'slate', children }) =>
    h(
        'span',
        {
            className: `inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold ${
                toneMap[tone] ?? toneMap.slate
            }`,
        },
        children,
    );

const Section = ({ title, description, actions, children }) =>
    h(
        'section',
        { className: 'rounded-2xl bg-white/80 p-6 shadow-sm ring-1 ring-slate-200' },
        [
            h(
                'div',
                { className: 'flex flex-col gap-2 md:flex-row md:items-start md:justify-between' },
                [
                    h('div', { className: 'space-y-1' }, [
                        h('h2', { className: 'text-lg font-bold text-slate-900' }, title),
                        description
                            ? h('p', { className: 'text-sm text-slate-600' }, description)
                            : null,
                    ]),
                    actions ? h('div', { className: 'flex flex-wrap gap-2' }, actions) : null,
                ],
            ),
            h('div', { className: 'mt-4 space-y-4' }, children),
        ],
    );

const normalizeCollection = (payload) => {
    if (!payload) return [];
    if (Array.isArray(payload.data)) return payload.data;
    if (Array.isArray(payload.items)) return payload.items;
    if (Array.isArray(payload)) return payload;
    return [];
};

const formatNumber = (value) =>
    typeof value === 'number' && !Number.isNaN(value) ? value.toFixed(2) : '—';

function App() {
    const [orders, setOrders] = useState([]);
    const [ordersMeta, setOrdersMeta] = useState({});
    const [ordersLoading, setOrdersLoading] = useState(false);
    const [orderFilters, setOrderFilters] = useState({ status: '', delivery_type: '' });

    const [inventoryBatches, setInventoryBatches] = useState([]);
    const [inventoryMovements, setInventoryMovements] = useState([]);

    const [shifts, setShifts] = useState([]);
    const [recipes, setRecipes] = useState([]);

    const [message, setMessage] = useState('');
    const [error, setError] = useState('');

    const [inboundForm, setInboundForm] = useState({
        product_id: '',
        supplier_id: '',
        store_id: '',
        buy_price: '',
        qty: '',
        arrived_at: '',
        expires_at: '',
        reason: '',
        note: '',
    });
    const [outboundForm, setOutboundForm] = useState({ product_id: '', qty: '', reason: '' });
    const [adjustForm, setAdjustForm] = useState({
        product_id: '',
        batch_id: '',
        qty: '',
        type: 'adjust',
        reason: '',
        note: '',
    });

    const [shiftForm, setShiftForm] = useState({ store_id: '', cash_start: '', cash_end: '', shift_id: '' });

    const [recipeForm, setRecipeForm] = useState({ bouquet_product_id: '', name: '', itemsInput: '' });

    const handleError = (err, fallback) => {
        console.error(err);
        const apiMessage = err?.response?.data?.message;
        setError(apiMessage || fallback || 'Что-то пошло не так.');
    };

    const resetAlerts = () => {
        setMessage('');
        setError('');
    };

    const loadOrders = async (filters = orderFilters) => {
        setOrdersLoading(true);
        resetAlerts();
        try {
            const { data } = await api.get('/orders', { params: filters });
            setOrders(normalizeCollection(data));
            setOrdersMeta(data.meta || {});
        } catch (err) {
            handleError(err, 'Не удалось загрузить заказы');
        } finally {
            setOrdersLoading(false);
        }
    };

    const loadInventory = async () => {
        resetAlerts();
        try {
            const [batchesRes, movementsRes] = await Promise.all([
                api.get('/inventory/batches'),
                api.get('/inventory/movements'),
            ]);
            setInventoryBatches(normalizeCollection(batchesRes.data));
            setInventoryMovements(normalizeCollection(movementsRes.data));
        } catch (err) {
            handleError(err, 'Не удалось загрузить склад');
        }
    };

    const loadShifts = async () => {
        resetAlerts();
        try {
            const { data } = await api.get('/cash-shifts');
            setShifts(normalizeCollection(data));
        } catch (err) {
            handleError(err, 'Не удалось загрузить смены');
        }
    };

    const loadRecipes = async () => {
        resetAlerts();
        try {
            const { data } = await api.get('/bouquet-recipes');
            setRecipes(normalizeCollection(data));
        } catch (err) {
            handleError(err, 'Не удалось загрузить рецепты');
        }
    };

    useEffect(() => {
        loadOrders();
        loadInventory();
        loadShifts();
        loadRecipes();
    }, []);

    useEffect(() => {
        loadOrders(orderFilters);
        // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [orderFilters.status, orderFilters.delivery_type]);

    const updateOrderStatus = async (orderId, status) => {
        resetAlerts();
        try {
            await api.post(`/orders/${orderId}/status`, { status });
            setMessage('Статус заказа обновлён');
            await loadOrders();
        } catch (err) {
            handleError(err, 'Не удалось обновить статус заказа');
        }
    };

    const completeOrder = async (orderId) => {
        resetAlerts();
        try {
            await api.post(`/orders/${orderId}/complete`);
            setMessage('Заказ закрыт и товары списаны');
            await Promise.all([loadOrders(), loadInventory()]);
        } catch (err) {
            handleError(err, 'Не удалось закрыть заказ');
        }
    };

    const submitInbound = async (event) => {
        event.preventDefault();
        resetAlerts();
        try {
            await api.post('/inventory/in', inboundForm);
            setMessage('Поступление создано');
            setInboundForm({
                product_id: '',
                supplier_id: '',
                store_id: '',
                buy_price: '',
                qty: '',
                arrived_at: '',
                expires_at: '',
                reason: '',
                note: '',
            });
            await loadInventory();
        } catch (err) {
            handleError(err, 'Не удалось добавить поступление');
        }
    };

    const submitOutbound = async (event) => {
        event.preventDefault();
        resetAlerts();
        try {
            await api.post('/inventory/out', outboundForm);
            setMessage('Списание выполнено');
            setOutboundForm({ product_id: '', qty: '', reason: '' });
            await loadInventory();
        } catch (err) {
            handleError(err, 'Не удалось списать товары');
        }
    };

    const submitAdjustment = async (event) => {
        event.preventDefault();
        resetAlerts();
        try {
            await api.post('/inventory/adjust', adjustForm);
            setMessage('Корректировка проведена');
            setAdjustForm({ product_id: '', batch_id: '', qty: '', type: 'adjust', reason: '', note: '' });
            await loadInventory();
        } catch (err) {
            handleError(err, 'Не удалось скорректировать остатки');
        }
    };

    const openShift = async (event) => {
        event.preventDefault();
        resetAlerts();
        try {
            await api.post('/cash-shifts/open', {
                store_id: shiftForm.store_id || null,
                cash_start: shiftForm.cash_start,
            });
            setMessage('Смена открыта');
            setShiftForm({ ...shiftForm, cash_start: '' });
            await loadShifts();
        } catch (err) {
            handleError(err, 'Не удалось открыть смену');
        }
    };

    const closeShift = async (event) => {
        event.preventDefault();
        if (!shiftForm.shift_id) {
            setError('Выберите смену для закрытия');
            return;
        }

        resetAlerts();
        try {
            await api.post(`/cash-shifts/${shiftForm.shift_id}/close`, { cash_end: shiftForm.cash_end });
            setMessage('Смена закрыта');
            setShiftForm({ store_id: '', cash_start: '', cash_end: '', shift_id: '' });
            await loadShifts();
        } catch (err) {
            handleError(err, 'Не удалось закрыть смену');
        }
    };

    const parseRecipeItems = (text) =>
        text
            .split('\n')
            .map((line) => line.trim())
            .filter(Boolean)
            .map((line) => {
                const [product, qty] = line.split(':').map((part) => part.trim());
                return { product_id: Number(product), qty: Number(qty || 1) };
            })
            .filter((item) => item.product_id && item.qty);

    const createRecipe = async (event) => {
        event.preventDefault();
        resetAlerts();
        try {
            const items = parseRecipeItems(recipeForm.itemsInput);
            await api.post('/bouquet-recipes', {
                bouquet_product_id: recipeForm.bouquet_product_id,
                name: recipeForm.name,
                items,
            });
            setMessage('Рецепт сохранён');
            setRecipeForm({ bouquet_product_id: '', name: '', itemsInput: '' });
            await loadRecipes();
        } catch (err) {
            handleError(err, 'Не удалось сохранить рецепт');
        }
    };

    const openShifts = useMemo(() => shifts.filter((shift) => !shift.closed_at), [shifts]);

    return h('div', { className: 'min-h-screen bg-slate-50 px-4 py-8 md:px-8' }, [
        h('div', { className: 'mx-auto max-w-7xl space-y-6' }, [
            h('header', { className: 'flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between' }, [
                h('div', { className: 'space-y-1' }, [
                    h('p', { className: 'text-xs uppercase tracking-wide text-amber-600' }, 'CRM панели'),
                    h('h1', { className: 'text-2xl font-black text-slate-900' }, 'Операционный центр'),
                    h(
                        'p',
                        { className: 'text-sm text-slate-600' },
                        'Живая панель для заказов, склада, смен и рецептур букетов.',
                    ),
                ]),
                h('div', { className: 'flex gap-2' }, [
                    h(
                        'button',
                        {
                            onClick: () => {
                                loadOrders();
                                loadInventory();
                                loadShifts();
                                loadRecipes();
                            },
                            className:
                                'inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-slate-500',
                        },
                        'Обновить всё',
                    ),
                ]),
            ]),

            message
                ? h('div', { className: 'rounded-xl border border-green-100 bg-green-50 px-4 py-3 text-sm text-green-800' }, message)
                : null,
            error
                ? h('div', { className: 'rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-800' }, error)
                : null,

            Section({
                title: 'Заказы',
                description: 'Лента заказов и быстрые статусы.',
                actions: [
                    h(
                        'div',
                        { className: 'flex gap-2' },
                        [
                            h(
                                'select',
                                {
                                    className: 'rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-amber-500 focus:ring-amber-500',
                                    value: orderFilters.status,
                                    onChange: (event) => setOrderFilters({ ...orderFilters, status: event.target.value }),
                                },
                                [
                                    h('option', { value: '' }, 'Статус: все'),
                                    h('option', { value: 'new' }, 'Новый'),
                                    h('option', { value: 'completed' }, 'Завершён'),
                                    h('option', { value: 'cancelled' }, 'Отменён'),
                                ],
                            ),
                            h(
                                'select',
                                {
                                    className: 'rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 shadow-sm focus:border-amber-500 focus:ring-amber-500',
                                    value: orderFilters.delivery_type,
                                    onChange: (event) =>
                                        setOrderFilters({ ...orderFilters, delivery_type: event.target.value }),
                                },
                                [
                                    h('option', { value: '' }, 'Доставка: все'),
                                    h('option', { value: 'delivery' }, 'Доставка'),
                                    h('option', { value: 'pickup' }, 'Самовывоз'),
                                    h('option', { value: 'instore' }, 'В магазине'),
                                ],
                            ),
                        ],
                    ),
                ],
                children: [
                    ordersLoading
                        ? h('div', { className: 'flex items-center gap-2 text-sm text-slate-600' }, [
                              h('span', { className: 'h-2 w-2 animate-ping rounded-full bg-amber-500' }),
                              'Загружаем заказы...',
                          ])
                        : h(
                              'div',
                              { className: 'overflow-hidden rounded-xl border border-slate-200 shadow-sm' },
                              h(
                                  'div',
                                  { className: 'overflow-x-auto' },
                                  h(
                                      'table',
                                      { className: 'min-w-full divide-y divide-slate-200' },
                                      [
                                          h(
                                              'thead',
                                              { className: 'bg-slate-50' },
                                              h('tr', null, [
                                                  h('th', { className: 'px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500' }, '#'),
                                                  h('th', { className: 'px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500' }, 'Клиент'),
                                                  h('th', { className: 'px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500' }, 'Тип'),
                                                  h('th', { className: 'px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500' }, 'Сумма'),
                                                  h('th', { className: 'px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500' }, 'Оплата'),
                                                  h('th', { className: 'px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500' }, 'Статус'),
                                                  h('th', { className: 'px-4 py-3 text-left text-xs font-semibold uppercase text-slate-500' }, ''),
                                              ]),
                                          ),
                                          h(
                                              'tbody',
                                              { className: 'divide-y divide-slate-200 bg-white' },
                                              orders.length
                                                  ? orders.map((order) =>
                                                        h('tr', { key: order.id }, [
                                                            h('td', { className: 'px-4 py-3 text-sm font-semibold text-slate-900' }, `#${order.id}`),
                                                            h('td', { className: 'px-4 py-3 text-sm text-slate-700' }, order.customer?.name || '—'),
                                                            h('td', { className: 'px-4 py-3 text-sm text-slate-700' }, order.delivery_type || '—'),
                                                            h('td', { className: 'px-4 py-3 text-sm font-semibold text-slate-900' }, `${formatNumber(order.total)} ₸`),
                                                            h('td', { className: 'px-4 py-3 text-xs text-slate-600' }, [
                                                                h(StatusPill, {
                                                                    tone: order.payment_status === 'paid' ? 'green' : 'amber',
                                                                    children: order.payment_status || '—',
                                                                }),
                                                                h('div', { className: 'text-[11px] text-slate-500' }, `Оплачено: ${formatNumber(order.paid_total)} ₸`),
                                                            ]),
                                                            h('td', { className: 'px-4 py-3 text-sm text-slate-700' }, [
                                                                h(StatusPill, {
                                                                    tone:
                                                                        order.status === 'completed'
                                                                            ? 'green'
                                                                            : order.status === 'cancelled'
                                                                            ? 'rose'
                                                                            : 'blue',
                                                                    children: order.status || '—',
                                                                }),
                                                                order.delivery_time
                                                                    ? h(
                                                                          'div',
                                                                          { className: 'text-[11px] text-slate-500' },
                                                                          `Доставка: ${new Date(order.delivery_time).toLocaleString()}`,
                                                                      )
                                                                    : null,
                                                            ]),
                                                            h('td', { className: 'px-4 py-3 text-sm' },
                                                                h('div', { className: 'flex flex-wrap gap-2' }, [
                                                                    h(
                                                                        'button',
                                                                        {
                                                                            className:
                                                                                'rounded-lg border border-slate-200 px-3 py-1 text-xs font-semibold text-slate-700 hover:border-slate-300',
                                                                            onClick: () => updateOrderStatus(order.id, 'cancelled'),
                                                                        },
                                                                        'Отменить',
                                                                    ),
                                                                    h(
                                                                        'button',
                                                                        {
                                                                            className:
                                                                                'rounded-lg bg-amber-600 px-3 py-1 text-xs font-semibold text-white shadow hover:bg-amber-500',
                                                                            onClick: () => updateOrderStatus(order.id, 'new'),
                                                                        },
                                                                        'Вернуть в работу',
                                                                    ),
                                                                    h(
                                                                        'button',
                                                                        {
                                                                            className:
                                                                                'rounded-lg bg-green-600 px-3 py-1 text-xs font-semibold text-white shadow hover:bg-green-500',
                                                                            onClick: () => completeOrder(order.id),
                                                                        },
                                                                        'Выполнен',
                                                                    ),
                                                                ]),
                                                            ),
                                                        ]),
                                                    )
                                                  : h('tr', null, h('td', { className: 'px-4 py-6 text-center text-sm text-slate-500', colSpan: 7 }, 'Пока нет заказов')),
                                          ),
                                      ],
                                  ),
                              ),
                          ),
                    ordersMeta?.total
                        ? h(
                              'div',
                              { className: 'text-xs text-slate-500' },
                              `Всего записей: ${ordersMeta.total}`,
                          )
                        : null,
                ],
            }),

            Section({
                title: 'Склад',
                description: 'Поступления, списания и движения остатков.',
                children: [
                    h('div', { className: 'grid gap-4 lg:grid-cols-3' }, [
                        h('form', { onSubmit: submitInbound, className: 'space-y-3 rounded-xl border border-slate-200 bg-slate-50 p-4' }, [
                            h('p', { className: 'text-sm font-semibold text-slate-900' }, 'Поступление'),
                            h('input', {
                                className: 'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                placeholder: 'ID товара',
                                required: true,
                                value: inboundForm.product_id,
                                onChange: (event) => setInboundForm({ ...inboundForm, product_id: event.target.value }),
                            }),
                            h('div', { className: 'grid grid-cols-2 gap-2' }, [
                                h('input', {
                                    className:
                                        'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                    placeholder: 'Поставщик (ID)',
                                    value: inboundForm.supplier_id,
                                    onChange: (event) =>
                                        setInboundForm({ ...inboundForm, supplier_id: event.target.value }),
                                }),
                                h('input', {
                                    className:
                                        'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                    placeholder: 'Магазин (ID)',
                                    value: inboundForm.store_id,
                                    onChange: (event) => setInboundForm({ ...inboundForm, store_id: event.target.value }),
                                }),
                            ]),
                            h('div', { className: 'grid grid-cols-2 gap-2' }, [
                                h('input', {
                                    className:
                                        'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                    placeholder: 'Цена закупки',
                                    type: 'number',
                                    step: '0.01',
                                    required: true,
                                    value: inboundForm.buy_price,
                                    onChange: (event) => setInboundForm({ ...inboundForm, buy_price: event.target.value }),
                                }),
                                h('input', {
                                    className:
                                        'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                    placeholder: 'Количество',
                                    type: 'number',
                                    step: '0.001',
                                    required: true,
                                    value: inboundForm.qty,
                                    onChange: (event) => setInboundForm({ ...inboundForm, qty: event.target.value }),
                                }),
                            ]),
                            h('div', { className: 'grid grid-cols-2 gap-2' }, [
                                h('input', {
                                    className:
                                        'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                    placeholder: 'Прибыло',
                                    type: 'datetime-local',
                                    value: inboundForm.arrived_at,
                                    onChange: (event) => setInboundForm({ ...inboundForm, arrived_at: event.target.value }),
                                }),
                                h('input', {
                                    className:
                                        'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                    placeholder: 'Годен до',
                                    type: 'datetime-local',
                                    value: inboundForm.expires_at,
                                    onChange: (event) => setInboundForm({ ...inboundForm, expires_at: event.target.value }),
                                }),
                            ]),
                            h('textarea', {
                                className:
                                    'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                placeholder: 'Комментарий / причина',
                                rows: 2,
                                value: inboundForm.reason,
                                onChange: (event) => setInboundForm({ ...inboundForm, reason: event.target.value }),
                            }),
                            h('textarea', {
                                className:
                                    'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                placeholder: 'Заметки',
                                rows: 2,
                                value: inboundForm.note,
                                onChange: (event) => setInboundForm({ ...inboundForm, note: event.target.value }),
                            }),
                            h(
                                'button',
                                {
                                    type: 'submit',
                                    className:
                                        'w-full rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-500',
                                },
                                'Добавить поступление',
                            ),
                        ]),
                        h('form', { onSubmit: submitOutbound, className: 'space-y-3 rounded-xl border border-slate-200 bg-slate-50 p-4' }, [
                            h('p', { className: 'text-sm font-semibold text-slate-900' }, 'Списание'),
                            h('input', {
                                className: 'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                placeholder: 'ID товара',
                                required: true,
                                value: outboundForm.product_id,
                                onChange: (event) => setOutboundForm({ ...outboundForm, product_id: event.target.value }),
                            }),
                            h('input', {
                                className:
                                    'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                placeholder: 'Количество',
                                type: 'number',
                                step: '0.001',
                                required: true,
                                value: outboundForm.qty,
                                onChange: (event) => setOutboundForm({ ...outboundForm, qty: event.target.value }),
                            }),
                            h('textarea', {
                                className:
                                    'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                placeholder: 'Причина списания',
                                rows: 2,
                                value: outboundForm.reason,
                                onChange: (event) => setOutboundForm({ ...outboundForm, reason: event.target.value }),
                            }),
                            h(
                                'button',
                                {
                                    type: 'submit',
                                    className:
                                        'w-full rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-amber-500',
                                },
                                'Списать',
                            ),
                        ]),
                        h('form', { onSubmit: submitAdjustment, className: 'space-y-3 rounded-xl border border-slate-200 bg-slate-50 p-4' }, [
                            h('p', { className: 'text-sm font-semibold text-slate-900' }, 'Корректировка'),
                            h('div', { className: 'grid grid-cols-2 gap-2' }, [
                                h('input', {
                                    className:
                                        'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                    placeholder: 'ID товара',
                                    required: true,
                                    value: adjustForm.product_id,
                                    onChange: (event) => setAdjustForm({ ...adjustForm, product_id: event.target.value }),
                                }),
                                h('input', {
                                    className:
                                        'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                    placeholder: 'Партия (ID)',
                                    value: adjustForm.batch_id,
                                    onChange: (event) => setAdjustForm({ ...adjustForm, batch_id: event.target.value }),
                                }),
                            ]),
                            h('div', { className: 'grid grid-cols-2 gap-2' }, [
                                h('input', {
                                    className:
                                        'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                    placeholder: 'Изменение количества',
                                    type: 'number',
                                    step: '0.001',
                                    required: true,
                                    value: adjustForm.qty,
                                    onChange: (event) => setAdjustForm({ ...adjustForm, qty: event.target.value }),
                                }),
                                h('select', {
                                    className:
                                        'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-amber-500 focus:ring-amber-500',
                                    value: adjustForm.type,
                                    onChange: (event) => setAdjustForm({ ...adjustForm, type: event.target.value }),
                                }, [
                                    h('option', { value: 'adjust' }, 'Корректировка'),
                                    h('option', { value: 'waste' }, 'Списание потерь'),
                                ]),
                            ]),
                            h('textarea', {
                                className:
                                    'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                placeholder: 'Причина/комментарий',
                                rows: 2,
                                value: adjustForm.reason,
                                onChange: (event) => setAdjustForm({ ...adjustForm, reason: event.target.value }),
                            }),
                            h('textarea', {
                                className:
                                    'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                placeholder: 'Заметки',
                                rows: 2,
                                value: adjustForm.note,
                                onChange: (event) => setAdjustForm({ ...adjustForm, note: event.target.value }),
                            }),
                            h(
                                'button',
                                {
                                    type: 'submit',
                                    className:
                                        'w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-800',
                                },
                                'Сохранить',
                            ),
                        ]),
                    ]),

                    h('div', { className: 'grid gap-4 lg:grid-cols-2' }, [
                        h('div', { className: 'overflow-hidden rounded-xl border border-slate-200 bg-white' }, [
                            h('div', { className: 'border-b border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900' }, 'Партии'),
                            h(
                                'div',
                                { className: 'overflow-x-auto' },
                                h(
                                    'table',
                                    { className: 'min-w-full divide-y divide-slate-200 text-sm' },
                                    [
                                        h(
                                            'thead',
                                            { className: 'bg-slate-50 text-xs uppercase text-slate-500' },
                                            h('tr', null, [
                                                h('th', { className: 'px-4 py-2 text-left' }, 'Товар'),
                                                h('th', { className: 'px-4 py-2 text-left' }, 'Остаток'),
                                                h('th', { className: 'px-4 py-2 text-left' }, 'Закупка'),
                                                h('th', { className: 'px-4 py-2 text-left' }, 'Поставщик'),
                                            ]),
                                        ),
                                        h(
                                            'tbody',
                                            { className: 'divide-y divide-slate-200' },
                                            inventoryBatches.length
                                                ? inventoryBatches.map((batch) =>
                                                      h('tr', { key: batch.id }, [
                                                          h('td', { className: 'px-4 py-2 font-semibold text-slate-900' }, batch.product?.name || batch.product_id),
                                                          h('td', { className: 'px-4 py-2 text-slate-700' }, formatNumber(batch.balance_qty)),
                                                          h('td', { className: 'px-4 py-2 text-slate-700' }, `${formatNumber(batch.buy_price)} ₸`),
                                                          h('td', { className: 'px-4 py-2 text-slate-700' }, batch.supplier?.name || '—'),
                                                      ]),
                                                  )
                                                : h('tr', null, h('td', { className: 'px-4 py-4 text-center text-slate-500', colSpan: 4 }, 'Нет партий')),
                                        ),
                                    ],
                                ),
                            ),
                        ]),
                        h('div', { className: 'overflow-hidden rounded-xl border border-slate-200 bg-white' }, [
                            h('div', { className: 'border-b border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900' }, 'Движение остатков'),
                            h(
                                'div',
                                { className: 'overflow-x-auto' },
                                h(
                                    'table',
                                    { className: 'min-w-full divide-y divide-slate-200 text-sm' },
                                    [
                                        h(
                                            'thead',
                                            { className: 'bg-slate-50 text-xs uppercase text-slate-500' },
                                            h('tr', null, [
                                                h('th', { className: 'px-4 py-2 text-left' }, 'Товар'),
                                                h('th', { className: 'px-4 py-2 text-left' }, 'Кол-во'),
                                                h('th', { className: 'px-4 py-2 text-left' }, 'Тип'),
                                                h('th', { className: 'px-4 py-2 text-left' }, 'Примечание'),
                                            ]),
                                        ),
                                        h(
                                            'tbody',
                                            { className: 'divide-y divide-slate-200' },
                                            inventoryMovements.length
                                                ? inventoryMovements.map((move) =>
                                                      h('tr', { key: move.id }, [
                                                          h('td', { className: 'px-4 py-2 font-semibold text-slate-900' }, move.product?.name || move.product_id),
                                                          h('td', { className: 'px-4 py-2 text-slate-700' }, formatNumber(move.qty)),
                                                          h('td', { className: 'px-4 py-2 text-slate-700' }, move.type || '—'),
                                                          h('td', { className: 'px-4 py-2 text-slate-700' }, move.note || move.reason || '—'),
                                                      ]),
                                                  )
                                                : h('tr', null, h('td', { className: 'px-4 py-4 text-center text-slate-500', colSpan: 4 }, 'Нет движений')),
                                        ),
                                    ],
                                ),
                            ),
                        ]),
                    ]),
                ],
            }),

            Section({
                title: 'Кассовые смены',
                description: 'Контроль открытия и закрытия смен.',
                children: [
                    h('div', { className: 'grid gap-4 lg:grid-cols-2' }, [
                        h('form', { onSubmit: openShift, className: 'space-y-3 rounded-xl border border-slate-200 bg-slate-50 p-4' }, [
                            h('p', { className: 'text-sm font-semibold text-slate-900' }, 'Открыть смену'),
                            h('input', {
                                className: 'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                placeholder: 'Магазин (ID)',
                                value: shiftForm.store_id,
                                onChange: (event) => setShiftForm({ ...shiftForm, store_id: event.target.value }),
                            }),
                            h('input', {
                                className: 'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                placeholder: 'Наличные на начало смены',
                                type: 'number',
                                step: '0.01',
                                required: true,
                                value: shiftForm.cash_start,
                                onChange: (event) => setShiftForm({ ...shiftForm, cash_start: event.target.value }),
                            }),
                            h(
                                'button',
                                {
                                    type: 'submit',
                                    className:
                                        'w-full rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-slate-800',
                                },
                                'Открыть',
                            ),
                        ]),
                        h('form', { onSubmit: closeShift, className: 'space-y-3 rounded-xl border border-slate-200 bg-slate-50 p-4' }, [
                            h('p', { className: 'text-sm font-semibold text-slate-900' }, 'Закрыть смену'),
                            h('select', {
                                className:
                                    'w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-800 focus:border-amber-500 focus:ring-amber-500',
                                value: shiftForm.shift_id,
                                onChange: (event) => setShiftForm({ ...shiftForm, shift_id: event.target.value }),
                            }, [
                                h('option', { value: '' }, 'Выберите открытую смену'),
                                ...openShifts.map((shift) =>
                                    h(
                                        'option',
                                        { key: shift.id, value: shift.id },
                                        `#${shift.id} — открыта ${new Date(shift.opened_at).toLocaleString()}`,
                                    ),
                                ),
                            ]),
                            h('input', {
                                className: 'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                placeholder: 'Наличные на конец',
                                type: 'number',
                                step: '0.01',
                                required: true,
                                value: shiftForm.cash_end,
                                onChange: (event) => setShiftForm({ ...shiftForm, cash_end: event.target.value }),
                            }),
                            h(
                                'button',
                                {
                                    type: 'submit',
                                    className:
                                        'w-full rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-amber-500',
                                },
                                'Закрыть',
                            ),
                        ]),
                    ]),

                    h('div', { className: 'overflow-hidden rounded-xl border border-slate-200 bg-white' }, [
                        h('div', { className: 'border-b border-slate-200 px-4 py-3 text-sm font-semibold text-slate-900' }, 'История смен'),
                        h(
                            'div',
                            { className: 'overflow-x-auto' },
                            h(
                                'table',
                                { className: 'min-w-full divide-y divide-slate-200 text-sm' },
                                [
                                    h(
                                        'thead',
                                        { className: 'bg-slate-50 text-xs uppercase text-slate-500' },
                                        h('tr', null, [
                                            h('th', { className: 'px-4 py-2 text-left' }, '#'),
                                            h('th', { className: 'px-4 py-2 text-left' }, 'Открыта'),
                                            h('th', { className: 'px-4 py-2 text-left' }, 'Закрыта'),
                                            h('th', { className: 'px-4 py-2 text-left' }, 'Наличные'),
                                            h('th', { className: 'px-4 py-2 text-left' }, 'Итог'),
                                        ]),
                                    ),
                                    h(
                                        'tbody',
                                        { className: 'divide-y divide-slate-200' },
                                        shifts.length
                                            ? shifts.map((shift) =>
                                                  h('tr', { key: shift.id }, [
                                                      h('td', { className: 'px-4 py-2 font-semibold text-slate-900' }, `#${shift.id}`),
                                                      h('td', { className: 'px-4 py-2 text-slate-700' }, new Date(shift.opened_at).toLocaleString()),
                                                      h('td', { className: 'px-4 py-2 text-slate-700' }, shift.closed_at ? new Date(shift.closed_at).toLocaleString() : '—'),
                                                      h('td', { className: 'px-4 py-2 text-slate-700' }, `${formatNumber(shift.cash_start)} → ${formatNumber(shift.cash_end)}`),
                                                      h('td', { className: 'px-4 py-2 text-slate-700' }, formatNumber(shift.cash_diff)),
                                                  ]),
                                              )
                                            : h('tr', null, h('td', { className: 'px-4 py-4 text-center text-slate-500', colSpan: 5 }, 'Смены не найдены')),
                                    ),
                                ],
                            ),
                        ),
                    ]),
                ],
            }),

            Section({
                title: 'Рецепты букетов',
                description: 'Конструктор рецептур и быстрый обзор состава.',
                children: [
                    h('form', { onSubmit: createRecipe, className: 'space-y-3 rounded-xl border border-slate-200 bg-slate-50 p-4' }, [
                        h('div', { className: 'grid gap-2 md:grid-cols-2' }, [
                            h('div', { className: 'space-y-2' }, [
                                h('label', { className: 'block text-xs font-semibold uppercase text-slate-500' }, 'Товар букет (ID)'),
                                h('input', {
                                    className:
                                        'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                    placeholder: 'ID товара букета',
                                    required: true,
                                    value: recipeForm.bouquet_product_id,
                                    onChange: (event) => setRecipeForm({ ...recipeForm, bouquet_product_id: event.target.value }),
                                }),
                            ]),
                            h('div', { className: 'space-y-2' }, [
                                h('label', { className: 'block text-xs font-semibold uppercase text-slate-500' }, 'Название рецепта'),
                                h('input', {
                                    className:
                                        'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                    placeholder: 'Весенний базовый',
                                    required: true,
                                    value: recipeForm.name,
                                    onChange: (event) => setRecipeForm({ ...recipeForm, name: event.target.value }),
                                }),
                            ]),
                        ]),
                        h('div', { className: 'space-y-2' }, [
                            h('label', { className: 'block text-xs font-semibold uppercase text-slate-500' }, 'Состав (product_id:qty в строке)'),
                            h('textarea', {
                                className:
                                    'w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-amber-500 focus:ring-amber-500',
                                placeholder: '12:3\n15:1\n18:0.5',
                                rows: 3,
                                value: recipeForm.itemsInput,
                                onChange: (event) => setRecipeForm({ ...recipeForm, itemsInput: event.target.value }),
                            }),
                        ]),
                        h(
                            'button',
                            {
                                type: 'submit',
                                className:
                                    'inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow hover:bg-green-500',
                            },
                            'Сохранить рецепт',
                        ),
                    ]),

                    h('div', { className: 'grid gap-4 md:grid-cols-2' },
                        recipes.length
                            ? recipes.map((recipe) =>
                                  h('div', { key: recipe.id, className: 'space-y-3 rounded-xl border border-slate-200 bg-white p-4 shadow-sm' }, [
                                      h('div', { className: 'flex items-center justify-between' }, [
                                          h('div', null, [
                                              h('p', { className: 'text-xs uppercase tracking-wide text-amber-600' }, `#${recipe.id}`),
                                              h('h3', { className: 'text-lg font-semibold text-slate-900' }, recipe.name),
                                              h('p', { className: 'text-sm text-slate-600' }, `Товар букета: ${recipe.bouquet_product_id}`),
                                          ]),
                                          h(StatusPill, {
                                              tone: 'blue',
                                              children: `${recipe.items?.length || 0} позиций`,
                                          }),
                                      ]),
                                      h(
                                          'div',
                                          { className: 'space-y-2' },
                                          recipe.items?.length
                                              ? recipe.items.map((item) =>
                                                    h('div', { key: item.id || `${recipe.id}-${item.product_id}` }, [
                                                        h('p', { className: 'text-sm font-semibold text-slate-900' }, `Товар ${item.product_id}`),
                                                        h('p', { className: 'text-xs text-slate-600' }, `Количество: ${item.qty}`),
                                                    ]),
                                                )
                                              : h('p', { className: 'text-sm text-slate-500' }, 'Нет состава'),
                                      ),
                                  ]),
                              )
                            : h('div', { className: 'rounded-xl border border-dashed border-slate-300 bg-white px-4 py-10 text-center text-sm text-slate-500' }, 'Рецепты пока не добавлены'),
                    ),
                ],
            }),
        ]),
    ]);
}

const mount = () => {
    const root = document.getElementById('crm-root');
    if (!root) return;
    createRoot(root).render(h(App));
};

document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', mount) : mount();
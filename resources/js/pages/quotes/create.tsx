import AppLayout from '@/layouts/app-layout'
import { quotes } from '@/routes';
import { BreadcrumbItem } from '@/types';
import { Head, router, useForm, usePage } from '@inertiajs/react'
import { Label } from '@radix-ui/react-label';
import ReactSelect, { CSSObjectWithLabel } from 'react-select';

// import { useEffect, useState } from 'react';
import { Input } from '@/components/ui/input';
import { Textarea } from "@/components/ui/textarea"
import { useState } from 'react';


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Crear Cotización",
        href: quotes().url,
    },
];

type Client = {
    id: number;
    name: string;
};

const CreateQuote = () => {
    // Formateador de CLP
    const formatCLP = (value: string | number) => {
        const num = Number(value);
        if (isNaN(num)) return '';
        return num.toLocaleString('es-CL', { style: 'currency', currency: 'CLP', minimumFractionDigits: 0 });
    };
    // Estado para descuento e impuestos
    const [discount, setDiscount] = useState<number>(0); // porcentaje
    const [tax, setTax] = useState<number>(0); // porcentaje

   
    const { data, setData, post, errors, processing } = useForm({
        client_id: '',
        title: '',
        description: '',
        total_amount: '',
        delivery_date: '',
        quote_items: [
            { quantity: 1, description: '', unit_price: '', total: '' },
        ],
    });

     // Calcular subtotal
    const subtotal = data.quote_items.reduce((acc, item) => acc + (Number(item.total) || 0), 0);
    // Calcular total con descuento e impuestos
    const discountAmount = subtotal * (discount / 100);
    const taxedAmount = (subtotal - discountAmount) * (tax / 100);
    const total = subtotal - discountAmount + taxedAmount;

    // Funciones para manejar items
    const handleItemChange = (idx: number, field: string, value: string | number) => {
        const items = [...data.quote_items];
        items[idx] = {
            ...items[idx],
            [field]: value,
        };
        // Calcular total automáticamente
        if (field === 'quantity' || field === 'unit_price') {
            const qty = Number(items[idx].quantity) || 0;
            const price = Number(items[idx].unit_price) || 0;
            items[idx].total = (qty * price).toFixed(2);
        }
        setData('quote_items', items);
    };

    const addItem = () => {
        setData('quote_items', [
            ...data.quote_items,
            { quantity: 1, description: '', unit_price: '', total: '' },
        ]);
    };

    const removeItem = (idx: number) => {
        const items = [...data.quote_items];
        items.splice(idx, 1);
        setData('quote_items', items);
    };

    const { clients } = usePage().props as unknown as { clients: Client[] };

    const submitForm = (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        post("/quotes", {
            onSuccess: () => {
                // Manejar el éxito, como redirigir o mostrar un mensaje
                console.log("Cotización creada con éxito");
            },
            onError: () => {
                console.log("Error al crear la cotización");
                // Manejar el error, como mostrar un mensaje de error
            },
        });
    };

    const selectStyles = {
        control: (provided: CSSObjectWithLabel) => ({
            ...provided,
            minHeight: '40px',
            height: '40px',
            boxShadow: 'none',
        }),
        valueContainer: (provided: CSSObjectWithLabel) => ({
            ...provided,
            height: '40px',
            padding: '0 8px',
        }),
        input: (provided: CSSObjectWithLabel) => ({
            ...provided,
            margin: '0px',
        }),
        indicatorsContainer: (provided: CSSObjectWithLabel) => ({
            ...provided,
            height: '40px',
        }),
    };
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Crear Cotización" />
            <div className="px-4 sm:p-6 lg:px-8">
                <h1 className="text-2xl font-semibold">Crear Nueva Cotización</h1>
            </div>
            <div className="px-4 sm:p-6 lg:px-8">
                {/* Formulario para crear una nueva cotización */}
                <form onSubmit={submitForm} className="flex flex-col w-full gap-6">
                    <div className="flex flex-col sm:flex-row w-full gap-4 mb-4">
                        <div className="flex flex-1 flex-col gap-2">
                            <Label htmlFor="title">Nombre de la Cotización</Label>
                            <Input
                                type="text"
                                id="title"
                                placeholder="Ej: Creación de ecommerce de ropa para empresa X"
                                value={data.title}
                                onChange={e => setData('title', e.target.value)}
                                required
                                style={{ height: '40px' }}
                            />
                            {errors.title && <span className="text-red-500 text-xs">{errors.title}</span>}
                        </div>
                        <div className="flex flex-1 flex-col gap-2">
                            <Label htmlFor="client_id">Cliente asociado</Label>
                            <ReactSelect
                                inputId="client_id"
                                name="client_id"
                                options={clients.map((client: Client) => ({ value: client.id, label: client.name }))}
                                value={clients.find((c: Client) => c.id === Number(data.client_id)) ? { value: Number(data.client_id), label: clients.find((c: Client) => c.id === Number(data.client_id))?.name } : null}
                                onChange={(option: { value: number; label: string | undefined } | null) => setData('client_id', option ? String(option.value) : '')}
                                placeholder="Selecciona un cliente"
                                isClearable
                                isSearchable
                                styles={selectStyles}
                            />
                            {errors.client_id && <span className="text-red-500 text-xs">{errors.client_id}</span>}
                        </div>
                    </div>
                    <div className="flex w-full flex-col gap-2 mb-4">
                        <Label htmlFor="description">Descripción General</Label>
                        <Textarea
                            id="description"
                            placeholder="Descripción de la cotización"
                            value={data.description}
                            rows={15}
                            style={{ minHeight: "130px" }}
                            onChange={e => setData('description', e.target.value)}
                            required
                        />
                        {errors.description && <span className="text-red-500 text-xs">{errors.description}</span>}
                    </div>

                    {/* Desglose de Servicios */}
                    <div className="w-full bg-muted/30 rounded-lg p-4 mb-4">
                        <h2 className="font-semibold mb-2">Desglose de Servicios</h2>
                        <div className="overflow-x-auto">
                            <table className="min-w-full text-sm border-separate border-spacing-y-2">
                                <thead>
                                    <tr className="text-muted-foreground">
                                        <th className="px-2 py-1 text-left">CANTIDAD</th>
                                        <th className="px-2 py-1 text-left">DESCRIPCIÓN</th>
                                        <th className="px-2 py-1 text-left">PRECIO UNIT.</th>
                                        <th className="px-2 py-1 text-left">TOTAL</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {data.quote_items.map((item, idx) => (
                                        <tr key={idx} className="bg-background border rounded">
                                            <td className="px-2 py-1">
                                                <Input
                                                    type="number"
                                                    min={1}
                                                    value={item.quantity}
                                                    onChange={e => handleItemChange(idx, 'quantity', Number(e.target.value))}
                                                    className="w-16"
                                                />
                                            </td>
                                            <td className="px-2 py-1">
                                                <Input
                                                    type="text"
                                                    value={item.description}
                                                    onChange={e => handleItemChange(idx, 'description', e.target.value)}
                                                    className="w-full"
                                                />
                                            </td>
                                            <td className="px-2 py-1">
                                                <div className="relative">
                                                    <Input
                                                        type="text"
                                                        inputMode="numeric"
                                                        pattern="[0-9]*"
                                                        value={item.unit_price !== '' ? formatCLP(item.unit_price) : ''}
                                                        onChange={e => {
                                                            // Eliminar todo lo que no sea número
                                                            let raw = e.target.value.replace(/[^\d]/g, '');
                                                            // Evitar ceros a la izquierda
                                                            if (raw.length > 1) raw = raw.replace(/^0+/, '');
                                                            handleItemChange(idx, 'unit_price', raw);
                                                        }}
                                                        className="w-24 pr-2 text-right"
                                                    />
                                                    <span className="absolute right-2 top-1/2 -translate-y-1/2 text-xs text-muted-foreground pointer-events-none">CLP</span>
                                                </div>
                                            </td>
                                            <td className="px-2 py-1 font-mono text-right align-middle">
                                                {Number(item.total).toLocaleString('es-CL', { style: 'currency', currency: 'CLP', minimumFractionDigits: 0 })}
                                            </td>
                                            <td className="px-2 py-1">
                                                {data.quote_items.length > 1 && (
                                                    <button type="button" onClick={() => removeItem(idx)} className="text-red-500 hover:underline text-xs">Eliminar</button>
                                                )}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                        <button type="button" onClick={addItem} className="mt-2 px-3 py-1 rounded bg-primary/10 text-primary hover:bg-primary/20 transition text-sm font-medium">+ Añadir Ítem</button>
                    </div>
                    {/* Resumen de totales */}
                    <div className="w-full flex flex-col items-end mt-8">
                        <div className="w-full max-w-md bg-muted/30 rounded-lg p-4">
                            <div className="flex justify-between items-center mb-2">
                                <span className="text-muted-foreground">Subtotal</span>
                                <span className="font-mono">{subtotal.toLocaleString('es-CL', { style: 'currency', currency: 'CLP', minimumFractionDigits: 0 })}</span>
                            </div>
                            <div className="flex justify-between items-center mb-2">
                                <span className="text-muted-foreground">Descuento (%)</span>
                                <input
                                    type="number"
                                    min={0}
                                    max={100}
                                    value={discount}
                                    onInput={e => {
                                        let value = (e.target as HTMLInputElement).value;
                                        // Eliminar todos los ceros a la izquierda
                                        value = value.replace(/^0+(?!$)/, '');
                                        // Sobrescribir el valor del input para evitar que el usuario vea ceros a la izquierda
                                        (e.target as HTMLInputElement).value = value;
                                        if (value === '') {
                                            setDiscount(0);
                                        } else {
                                            const num = Number(value);
                                            if (!isNaN(num) && num >= 0 && num <= 100) {
                                                setDiscount(num);
                                            }
                                        }
                                    }}
                                    onKeyDown={e => {
                                        // Bloquear letras, e, +, - y .
                                        if (["e", "E", "+", "-", "."].includes(e.key)) {
                                            e.preventDefault();
                                        }
                                    }}
                                    className="w-20 rounded border px-2 py-1 bg-background text-right"
                                />
                            </div>
                            <div className="flex justify-between items-center mb-2">
                                <span className="text-muted-foreground">Impuestos (%)</span>
                                <input
                                    type="number"
                                    min={0}
                                    max={100}
                                    value={tax}
                                    onInput={e => {
                                        let value = (e.target as HTMLInputElement).value;
                                        // Eliminar todos los ceros a la izquierda
                                        value = value.replace(/^0+(?!$)/, '');
                                        // Sobrescribir el valor del input para evitar que el usuario vea ceros a la izquierda
                                        (e.target as HTMLInputElement).value = value;
                                        if (value === '') {
                                            setTax(0);
                                        } else {
                                            const num = Number(value);
                                            if (!isNaN(num) && num >= 0 && num <= 100) {
                                                setTax(num);
                                            }
                                        }
                                    }}
                                    onKeyDown={e => {
                                        // Bloquear letras, e, +, - y .
                                        if (["e", "E", "+", "-", "."].includes(e.key)) {
                                            e.preventDefault();
                                        }
                                    }}
                                    className="w-20 rounded border px-2 py-1 bg-background text-right"
                                />
                            </div>
                            <div className="flex justify-between items-center border-t pt-3 mt-3">
                                <span className="font-semibold text-lg">Total</span>
                                <span className="font-bold text-primary text-lg">{total.toLocaleString('es-CL', { style: 'currency', currency: 'CLP', minimumFractionDigits: 0 })}</span>
                            </div>
                        </div>
                    </div>
                    <div className="w-full flex justify-end space-x-4 mt-4">
                        <button type="button" onClick={() => router.visit('/quotes')} className="mt-6 px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600 transition w-full sm:w-auto">
                            Cancelar
                        </button>
                        <button type="submit" disabled={processing} className="mt-6 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600 transition w-full sm:w-auto">
                        {processing ? 'Creando...' : 'Crear Cotización'}
                    </button>
                    </div>
                </form>
            </div>
        </AppLayout>
    )
}

export default CreateQuote
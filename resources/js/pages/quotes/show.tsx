import AppLayout from '@/layouts/app-layout';
import { Head } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';
import { BreadcrumbItem } from '@/types';

const formatCLP = (value: number) => value.toLocaleString('es-CL', { style: 'currency', currency: 'CLP', minimumFractionDigits: 0 });

const QuoteShow = () => {
    const { quote } = usePage().props as unknown as { quote: {
        id: number;
        client?: { id: number; name: string };
        title: string;
        description?: string;
        delivery_date?: string;
        quote_items: Array<{
            quantity: number;
            description: string;
            unit_price: number;
            total: number;
        }>;
        subtotal: number;
        discount?: number;
        tax?: number;
        total: number;
    } };
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Cotizaciones', href: '/quotes' },
        { title: `Cotización #${quote.id}`, href: `/quotes/${quote.id}` },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={`Cotización #${quote.id}`} />
            <div className="px-4 sm:p-6 lg:px-8">
                <h1 className="text-2xl font-semibold mb-2">Cotización #{quote.id}</h1>
                <div className="mb-4 text-muted-foreground">{quote.title}</div>
                <div className="mb-2"><span className="font-semibold">Cliente:</span> {quote.client?.name}</div>
                <div className="mb-2"><span className="font-semibold">Fecha de entrega:</span> {quote.delivery_date ? new Date(quote.delivery_date).toLocaleDateString('es-CL') : '-'}</div>
                <div className="mb-6"><span className="font-semibold">Descripción:</span> <br />{quote.description}</div>

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
                                </tr>
                            </thead>
                            <tbody>
                                {quote.quote_items.map((item: { quantity: number; description: string; unit_price: number; total: number; }, idx: number) => (
                                    <tr key={idx} className="bg-background border rounded">
                                        <td className="px-2 py-1">{item.quantity}</td>
                                        <td className="px-2 py-1">{item.description}</td>
                                        <td className="px-2 py-1 font-mono text-right">{formatCLP(item.unit_price)}</td>
                                        <td className="px-2 py-1 font-mono text-right">{formatCLP(item.total)}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>

                <div className="w-full flex flex-col items-end mt-8">
                    <div className="w-full max-w-md bg-muted/30 rounded-lg p-4">
                        <div className="flex justify-between items-center mb-2">
                            <span className="text-muted-foreground">Subtotal</span>
                            <span className="font-mono">{formatCLP(quote.subtotal)}</span>
                        </div>
                        <div className="flex justify-between items-center mb-2">
                            <span className="text-muted-foreground">Descuento (%)</span>
                            <span className="font-mono">{quote.discount ?? 0}</span>
                        </div>
                        <div className="flex justify-between items-center mb-2">
                            <span className="text-muted-foreground">Impuestos (%)</span>
                            <span className="font-mono">{quote.tax ?? 0}</span>
                        </div>
                        <div className="flex justify-between items-center border-t pt-3 mt-3">
                            <span className="font-semibold text-lg">Total</span>
                            <span className="font-bold text-primary text-lg">{formatCLP(quote.total)}</span>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
};

export default QuoteShow;

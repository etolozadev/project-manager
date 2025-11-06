import React, { useState } from "react";
import dayjs from "dayjs";
import "dayjs/locale/es";
import { DataTable } from "primereact/datatable";
import { Column } from "primereact/column";
import "primereact/resources/themes/lara-light-blue/theme.css";
import "primereact/resources/primereact.min.css";
import "primeicons/primeicons.css";
import AppLayout from "@/layouts/app-layout";
import { Head, Link } from "@inertiajs/react";
import { BreadcrumbItem } from "@/types";
import { dashboard } from "@/routes";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Plus } from "lucide-react";

type Quotation = {
    id: number;
    client: { id: number; name: string };
    title: string;
    description?: string;
    status: string;
    total_amount: string;
    delivery_date?: string;
    pdf_path?: string;
    created_at: string;
};

interface QuotesPageProps {
    quotations: Quotation[];
    clients: { id: number; name: string }[];
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Cotizaciones",
        href: dashboard().url,
    },
];

export default function Quotes(props: QuotesPageProps) {
    dayjs.locale("es");
    const [search, setSearch] = useState("");

    const filteredQuotations = (props.quotations || []).filter(q => {
        const searchLower = search.toLowerCase();
        return (
            q.title.toLowerCase().includes(searchLower) ||
            (q.client?.name?.toLowerCase() || "").includes(searchLower)
        );
    });

    const parseStatus = (status: string) => {
        switch (status) {
            case 'sent':
                return <Badge className="bg-blue-500">Enviada</Badge>;
            case 'accepted':
                return <Badge className="bg-green-500" >Aceptada</Badge>;
            case 'draft':
                return <Badge className="bg-yellow-500">Borrador</Badge>;
            case 'rejected':
                return <Badge className="bg-red-500">Rechazada</Badge>;
            default:
                return <Badge className="bg-gray-500">Desconocido</Badge>;
        }
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Cotizaciones" />
            <div className="p-8">
                <h1 className="text-2xl font-bold mb-4">Cotizaciones</h1>
                <div className="mb-4 flex justify-between gap-2">
                    <input
                        type="text"
                        placeholder="Buscar por título o cliente..."
                        className="border rounded px-3 py-2 w-80"
                        value={search}
                        onChange={e => setSearch(e.target.value)}
                    />
                    <Link href="/quotes/create">
                        <Button className="flex items-center cursor-pointer gap-2 bg-green-500 hover:bg-green-600">
                            <Plus className="size-4" />
                            Crear Cotización
                        </Button>
                    </Link>
                </div>
                <DataTable value={filteredQuotations} paginator rows={10} tableStyle={{ minWidth: '60rem' }}>
                  {/*   <Column field="id" header="ID" sortable></Column> */}
                    <Column field="title" header="Título" sortable></Column>
                    <Column field="client.name" header="Cliente" body={(row: Quotation) => row.client?.name}></Column>
                    <Column field="status" header="Estado" sortable filter filterMatchMode="contains" body={(row: Quotation) => parseStatus(row.status)}></Column>
                    <Column field="total_amount" header="Total" sortable body={(row: Quotation) => `$${new Intl.NumberFormat("es-ES").format(parseInt(row.total_amount))}`}></Column>
                    <Column field="delivery_date" header="Fecha de Entrega" sortable body={(row: Quotation) => row.delivery_date ? dayjs(row.delivery_date).format("DD-MM-YYYY") : ""}></Column>
                    <Column field="created_at" header="Fecha de Creación" sortable body={(row: Quotation) => dayjs(row.created_at).format("DD-MM-YYYY HH:mm")}></Column>
                </DataTable>
            </div>
        </AppLayout>
    );
}
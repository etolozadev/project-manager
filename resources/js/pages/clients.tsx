import React, { useState } from "react";
import { DataTable } from "primereact/datatable";
import { Column } from "primereact/column";
import "primereact/resources/themes/lara-light-blue/theme.css";
import "primereact/resources/primereact.min.css";
import "primeicons/primeicons.css";
import AppLayout from "@/layouts/app-layout";
import { Head, useForm } from "@inertiajs/react";
import { BreadcrumbItem } from "@/types";
import { Button } from "@/components/ui/button";
import { toast } from "sonner";
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from "@/components/ui/dialog";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";


type Client = {
    id: number;
    name: string;
    email?: string;
    phone?: string;
    company?: string;
    rut?: string;
    address?: string;
};

interface ClientsPageProps {
    clients: Client[];
}

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: "Clientes",
        href: "#",
    },
];

export default function Clients(props: ClientsPageProps) {
    const [search, setSearch] = useState("");
    const [open, setOpen] = useState(false);
    const [editClient, setEditClient] = useState<Client | null>(null);
    const [deleteClient, setDeleteClient] = useState<Client | null>(null);
    const { data, setData, post, put, delete: destroy, processing, reset, errors } = useForm({
        name: "",
        email: "",
        phone: "",
        company: "",
        rut: "",
        address: "",
    });

    const filteredClients = (props.clients || []).filter(c => {
        const searchLower = search.toLowerCase();
        return (
            (c.name || "").toLowerCase().includes(searchLower) ||
            (c.email || "").toLowerCase().includes(searchLower) ||
            (c.phone || "").toLowerCase().includes(searchLower) ||
            (c.company || "").toLowerCase().includes(searchLower) ||
            (c.rut || "").toLowerCase().includes(searchLower) ||
            (c.address || "").toLowerCase().includes(searchLower)
        );
    });

    const openCreate = () => {
        setEditClient(null);
        setData({
            name: "",
            email: "",
            phone: "",
            company: "",
            rut: "",
            address: "",
        });
        setOpen(true);
    };

    const openEdit = (client: Client) => {
        setEditClient(client);
        setData({
            name: client.name || "",
            email: client.email || "",
            phone: client.phone || "",
            company: client.company || "",
            rut: client.rut || "",
            address: client.address || "",
        });
        setOpen(true);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editClient) {
            put(`/clients/${editClient.id}`, {
                onSuccess: () => {
                    setOpen(false);
                    reset();
                    setEditClient(null);
                    toast.success("Cliente actualizado exitosamente");
                },
                onError: () => {
                    toast.error(Object.values(errors).join(", "));
                }
            });
        } else {
            post("/clients", {
                onSuccess: () => {
                    setOpen(false);
                    reset();
                    toast.success("Cliente creado exitosamente");
                },
                onError: () => {
                    toast.error(Object.values(errors).join(", "));
                }
            });
        }
    };

    const handleDelete = () => {
        if (!deleteClient) return;
        destroy(`/clients/${deleteClient.id}`, {
            onSuccess: () => {
                setDeleteClient(null);
                toast.success("Cliente eliminado exitosamente");
            },
            onError: () => {
                toast.error("Error al eliminar cliente");
            }
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Clientes" />
            <div className="p-8">
                <h1 className="text-2xl font-bold mb-4">Clientes</h1>
                <div className="mb-4 flex justify-between gap-2">
                    <input
                        type="text"
                        placeholder="Buscar por nombre, email, empresa, etc..."
                        className="border rounded px-3 py-2 w-80"
                        value={search}
                        onChange={e => setSearch(e.target.value)}
                    />
                    <Button className="flex items-center cursor-pointer gap-2 bg-green-500 hover:bg-green-600" onClick={openCreate}>
                        Nuevo Cliente
                    </Button>
                </div>

                {/* Modal Crear/Editar */}
                <Dialog open={open} onOpenChange={setOpen}>
                    <DialogContent className="sm:max-w-[425px]">
                        <form onSubmit={handleSubmit}>
                            <DialogHeader>
                                <DialogTitle>{editClient ? 'Editar Cliente' : 'Nuevo Cliente'}</DialogTitle>
                                <DialogDescription>
                                    {editClient ? 'Edita los campos para actualizar el cliente.' : 'Rellena los campos para crear un nuevo cliente.'}
                                </DialogDescription>
                            </DialogHeader>
                            <div className="grid gap-4 my-4">
                                <div className="grid gap-3">
                                    <Label htmlFor="name">Nombre</Label>
                                    <Input id="name" name="name" value={data.name} onChange={e => setData('name', e.target.value)} />
                                    {errors.name && <span className="text-red-500 text-xs">{errors.name}</span>}
                                </div>
                                <div className="grid gap-3">
                                    <Label htmlFor="email">Email</Label>
                                    <Input id="email" name="email" value={data.email} onChange={e => setData('email', e.target.value)} />
                                    {errors.email && <span className="text-red-500 text-xs">{errors.email}</span>}
                                </div>
                                <div className="grid gap-3">
                                    <Label htmlFor="phone">Teléfono</Label>
                                    <Input id="phone" name="phone" value={data.phone} onChange={e => setData('phone', e.target.value)} />
                                    {errors.phone && <span className="text-red-500 text-xs">{errors.phone}</span>}
                                </div>
                                <div className="grid gap-3">
                                    <Label htmlFor="company">Empresa</Label>
                                    <Input id="company" name="company" value={data.company} onChange={e => setData('company', e.target.value)} />
                                    {errors.company && <span className="text-red-500 text-xs">{errors.company}</span>}
                                </div>
                                <div className="grid gap-3">
                                    <Label htmlFor="rut">RUT</Label>
                                    <Input id="rut" name="rut" value={data.rut} onChange={e => setData('rut', e.target.value)} />
                                    {errors.rut && <span className="text-red-500 text-xs">{errors.rut}</span>}
                                </div>
                                <div className="grid gap-3">
                                    <Label htmlFor="address">Dirección</Label>
                                    <Input id="address" name="address" value={data.address} onChange={e => setData('address', e.target.value)} />
                                    {errors.address && <span className="text-red-500 text-xs">{errors.address}</span>}
                                </div>
                            </div>
                            <DialogFooter>
                                <DialogClose asChild>
                                    <Button variant="outline" type="button" onClick={() => setOpen(false)}>Cancelar</Button>
                                </DialogClose>
                                <Button type="submit" disabled={processing}>{editClient ? 'Actualizar' : 'Guardar'}</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                {/* Modal Eliminar */}
                <Dialog open={!!deleteClient} onOpenChange={v => { if (!v) setDeleteClient(null); }}>
                    <DialogContent className="sm:max-w-[400px]">
                        <DialogHeader>
                            <DialogTitle>Eliminar Cliente</DialogTitle>
                            <DialogDescription>
                                ¿Estás seguro de que deseas eliminar este cliente?
                            </DialogDescription>
                        </DialogHeader>
                        <div className="flex justify-end gap-2 mt-4">
                            <Button variant="outline" onClick={() => setDeleteClient(null)}>Cancelar</Button>
                            <Button variant="destructive" onClick={handleDelete}>Eliminar</Button>
                        </div>
                    </DialogContent>
                </Dialog>

                <DataTable value={filteredClients} paginator rows={5} tableStyle={{ minWidth: '50rem' }}>
                    <Column field="id" header="ID" sortable></Column>
                    <Column field="name" header="Nombre" sortable></Column>
                    <Column field="email" header="Email" sortable></Column>
                    <Column field="phone" header="Teléfono" sortable></Column>
                    <Column field="company" header="Empresa" sortable></Column>
                    <Column field="rut" header="RUT" sortable></Column>
                    <Column field="address" header="Dirección" sortable></Column>
                    <Column
                        header="Acciones"
                        body={(rowData: Client) => (
                            <div className="flex gap-2">
                                <Button size="sm" variant="outline" onClick={() => openEdit(rowData)}>Editar</Button>
                                <Button size="sm" variant="destructive" onClick={() => setDeleteClient(rowData)}>Eliminar</Button>
                            </div>
                        )}
                    ></Column>
                </DataTable>
            </div>
        </AppLayout>
    );
}

import React, { useState } from "react";
import { DataTable } from "primereact/datatable";
import { Column } from "primereact/column";
import "primereact/resources/themes/lara-light-blue/theme.css";
import "primereact/resources/primereact.min.css";
import "primeicons/primeicons.css";
import AppLayout from "@/layouts/app-layout";
import { Head, useForm } from "@inertiajs/react";
import { BreadcrumbItem } from "@/types";
import { dashboard } from "@/routes";
import { Plus } from "lucide-react";
import { Button } from "@/components/ui/button";
import { toast } from "sonner"

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
import { Select } from "@radix-ui/react-select";
import { SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";


type PageProps = {
    auth?: {
        user: User;
    };
    [key: string]: unknown;
};

type User = {
    id: number;
    name: string;
    email: string;
    roles: { name: string }[];
};




interface Role {
    id: number;
    name: string;
}

interface UsersPageProps extends PageProps {
    users: User[];
    roles?: Role[];
}

export default function Users(props: UsersPageProps) {


    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: "Usuarios",
            href: dashboard().url,
        },
    ];

    const [search, setSearch] = useState("");
    const [open, setOpen] = useState(false);
    const [editUser, setEditUser] = useState<User | null>(null);
    const [deleteUser, setDeleteUser] = useState<User | null>(null);
    const { data, setData, post, put, delete: destroy, processing, reset, errors } = useForm({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        role: "",
    });

    const filteredUsers = (props.users || []).filter(u => {
        const searchLower = search.toLowerCase();
        return (
            u.name.toLowerCase().includes(searchLower) ||
            u.email.toLowerCase().includes(searchLower) ||
            (u.roles.map(r => r.name.toLowerCase()).join(", ").includes(searchLower))
        );
    });

    const openCreate = () => {
        setEditUser(null);
        setData({
            name: "",
            email: "",
            password: "",
            password_confirmation: "",
            role: "",
        });
        setOpen(true);
    };

    const openEdit = (user: User) => {
        setEditUser(user);
        setData({
            name: user.name,
            email: user.email,
            password: "",
            password_confirmation: "",
            role: user.roles[0]?.name || "",
        });
        setOpen(true);
    };

    const handleSubmit = (e: React.FormEvent) => {
        e.preventDefault();
        if (editUser) {
            put(`/users/${editUser.id}`, {
                onSuccess: () => {
                    setOpen(false);
                    reset();
                    setEditUser(null);
                    toast.success("Usuario actualizado exitosamente");
                },
                onError: () => {
                    toast.error(Object.values(errors).join(", "));
                }
            });
        } else {
            post("/users", {
                onSuccess: () => {
                    setOpen(false);
                    reset();
                    toast.success("Usuario creado exitosamente");
                },
                onError: () => {
                    toast.error(Object.values(errors).join(", "));
                }
            });
        }
    };

    const handleDelete = () => {
        if (!deleteUser) return;
        destroy(`/users/${deleteUser.id}`, {
            onSuccess: () => {
                setDeleteUser(null);
                toast.success("Usuario eliminado exitosamente");
            },
            onError: () => {
                toast.error("Error al eliminar usuario");
            }
        });
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Usuarios" />
            <div className="p-8">
                <h1 className="text-2xl font-bold mb-4">Usuarios</h1>
                <div className="mb-4 flex justify-between gap-2">
                    <input
                        type="text"
                        placeholder="Buscar por nombre, email o rol..."
                        className="border rounded px-3 py-2 w-80"
                        value={search}
                        onChange={e => setSearch(e.target.value)}
                    />
                    <Button className="flex items-center cursor-pointer gap-2 bg-green-500 hover:bg-green-600" onClick={openCreate}>
                        <Plus className="size-4" />
                        Crear Usuario
                    </Button>
                </div>

                {/* Modal Crear/Editar */}
                <Dialog open={open} onOpenChange={setOpen}>
                    <DialogContent className="sm:max-w-[425px]">
                        <form onSubmit={handleSubmit}>
                            <DialogHeader>
                                <DialogTitle>{editUser ? 'Editar Usuario' : 'Crear Usuario'}</DialogTitle>
                                <DialogDescription>
                                    {editUser ? 'Edita los campos para actualizar el usuario.' : 'Rellena los campos para crear un nuevo usuario.'}
                                </DialogDescription>
                            </DialogHeader>
                            <div className="grid gap-4 py-4">
                                <div className="grid gap-3">
                                    <Label htmlFor="name-1">Nombre</Label>
                                    <Input id="name-1" name="name" required placeholder="Esteban Toloza" value={data.name} onChange={e => setData('name', e.target.value)} />
                                    {errors.name && <span className="text-red-500 text-xs">{errors.name}</span>}
                                </div>
                                <div className="grid gap-3">
                                    <Label htmlFor="email-1">Email</Label>
                                    <Input id="email-1" name="email" required placeholder="esteban@example.com" value={data.email} onChange={e => setData('email', e.target.value)} />
                                    {errors.email && <span className="text-red-500 text-xs">{errors.email}</span>}
                                </div>
                                <div className="grid gap-3">
                                    <Label htmlFor="password-1">Contraseña</Label>
                                    <Input id="password-1" type="password" name="password" required placeholder="********" value={data.password} onChange={e => setData('password', e.target.value)} />
                                    {errors.password && <span className="text-red-500 text-xs">{errors.password}</span>}
                                </div>
                                <div className="grid gap-3">
                                    <Label htmlFor="password_confirmation-1">Confirma la Contraseña</Label>
                                    <Input id="password_confirmation-1" type="password" name="password_confirmation" required placeholder="********" value={data.password_confirmation} onChange={e => setData('password_confirmation', e.target.value)} />
                                    {errors.password_confirmation && <span className="text-red-500 text-xs">{errors.password_confirmation}</span>}
                                </div>
                                <div className="grid gap-3">
                                    <Label htmlFor="role-1">Rol</Label>
                                    <Select name="role" value={data.role} onValueChange={value => setData('role', value)} required>
                                        <SelectTrigger>
                                            <SelectValue placeholder="Selecciona un rol" />
                                        </SelectTrigger>
                                        <SelectContent>
                                            {(props.roles || []).map((role: Role) => (
                                                <SelectItem key={role.id} value={role.name}>
                                                    {role.name}
                                                </SelectItem>
                                            ))}
                                        </SelectContent>
                                        {errors.role && <span className="text-red-500 text-xs">{errors.role}</span>}
                                    </Select>
                                </div>
                            </div>
                            <DialogFooter>
                                <DialogClose asChild>
                                    <Button variant="outline" type="button" onClick={() => setOpen(false)}>Cancel</Button>
                                </DialogClose>
                                <Button type="submit" disabled={processing}>{editUser ? 'Actualizar' : 'Guardar'}</Button>
                            </DialogFooter>
                        </form>
                    </DialogContent>
                </Dialog>

                {/* Modal Eliminar */}
                <Dialog open={!!deleteUser} onOpenChange={v => { if (!v) setDeleteUser(null); }}>
                    <DialogContent className="sm:max-w-[400px]">
                        <DialogHeader>
                            <DialogTitle>Eliminar Usuario</DialogTitle>
                            <DialogDescription>
                                ¿Estás seguro de que deseas eliminar este usuario?
                            </DialogDescription>
                        </DialogHeader>
                        <div className="flex justify-end gap-2 mt-4">
                            <Button variant="outline" onClick={() => setDeleteUser(null)}>Cancelar</Button>
                            <Button variant="destructive" onClick={handleDelete}>Eliminar</Button>
                        </div>
                    </DialogContent>
                </Dialog>

                <DataTable value={filteredUsers} paginator rows={5} tableStyle={{ minWidth: '50rem' }}>
                    <Column field="id" header="ID" sortable></Column>
                    <Column field="name" header="Nombre" sortable></Column>
                    <Column field="email" header="Email" sortable></Column>
                    <Column
                        header="Rol"
                        body={(rowData: User) => rowData.roles.map(r => r.name).join(', ')}
                    ></Column>
                    <Column
                        header="Acciones"
                        body={(rowData: User) => (
                            <div className="flex gap-2">
                                <Button size="sm" variant="outline" onClick={() => openEdit(rowData)}>Editar</Button>
                                <Button size="sm" variant="destructive" onClick={() => setDeleteUser(rowData)}>Eliminar</Button>
                            </div>
                        )}
                    ></Column>
                </DataTable>
            </div>
        </AppLayout>
    );
}


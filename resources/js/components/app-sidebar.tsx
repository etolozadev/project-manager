import { NavFooter } from '@/components/nav-footer';
import { NavMain } from '@/components/nav-main';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { Link } from '@inertiajs/react';
import {  ChartBarIncreasingIcon, FileUserIcon, FolderKanban, ListCheckIcon, Users } from 'lucide-react';
import AppLogo from './app-logo';

const mainNavItems: NavItem[] = [
    {
        title: 'Dashboard',
        href: dashboard(),
        icon: ChartBarIncreasingIcon,
    },
    {
        title: 'Cotizaciones',
        href: '/quotes',
        icon: FileUserIcon,
    },
    {
        title: 'Proyectos',
        href: '/projects',
        icon: FolderKanban,
    },
    {
        title: 'Tareas',
        href: '/tasks',
        icon: ListCheckIcon,
    },
    {
        title: 'Clientes',
        href: '/clients',
        icon: Users,
    }
];

const footerNavItems: NavItem[] = [
   {
        title: 'Usuarios',
        href: '/users',
        icon: Users,
    }
];

export function AppSidebar() {
    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <SidebarMenu>
                    <SidebarMenuItem>
                        <SidebarMenuButton size="lg" asChild>
                            <Link href={dashboard()} prefetch>
                                <AppLogo />
                            </Link>
                        </SidebarMenuButton>
                    </SidebarMenuItem>
                </SidebarMenu>
            </SidebarHeader>

            <SidebarContent>
                <NavMain items={mainNavItems} />
            </SidebarContent>

            <SidebarFooter>
                <NavFooter items={footerNavItems} className="mt-auto" />
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}

import { InertiaLinkProps } from '@inertiajs/vue3';
import type { LucideIcon } from 'lucide-vue-next';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface NavItem {
    title: string;
    href: NonNullable<InertiaLinkProps['href']>;
    icon?: LucideIcon;
    isActive?: boolean;
}

export type AppPageProps<
    T extends Record<string, unknown> = Record<string, unknown>,
> = T & {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    sidebarOpen: boolean;
};

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    created_at: string;
    updated_at: string;
}

export type BreadcrumbItemType = BreadcrumbItem;

export interface Organization {
    uuid: string;
    name: string;
    type: string;
    website?: string;
    base_url?: string;
    status: string;
    registration_number?: string;
    tin?: string;
    last_synced_at?: string | null;
    last_sync_status?: string | null;
    last_sync_error?: string | null;
    endpoints?: Record<string, string>;
    products?: Product[];
    created_at: string;
    updated_at: string;
}

export interface Product {
    uuid: string;
    organization_uuid: string;
    name: string;
    type: string;
    currency_code: number;
    interest_rate?: number | null;
    fees?: number | null;
    term_months?: number | null;
    min_amount?: number | null;
    max_amount?: number | null;
    eligibility?: string | null;
    description?: string | null;
    extra?: Record<string, any> | null;
    remote_updated_at?: string | null;
    created_at: string;
    updated_at: string;
}

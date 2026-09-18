<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const showingNavigationDropdown = ref(false);
const { t, locale } = useI18n();

const changeLanguage = (lang) => {
    locale.value = lang;
    localStorage.setItem('locale', lang);
};
</script>

<template>
    <div>
        <div class="min-h-screen bg-gray-100">
            <nav
                class="border-b border-gray-100 bg-white"
            >
                <!-- Primary Navigation Menu -->
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between">
                        <div class="flex">
                            <!-- Logo -->
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800"
                                    />
                                </Link>
                            </div>

                            <!-- Navigation Links -->
                            <div
                                class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex"
                            >
                                <NavLink
                                    :href="route('dashboard')"
                                    :active="route().current('dashboard')"
                                >
                                    {{ t('dashboard') }}
                                </NavLink>
                                <NavLink
                                    v-if="$page.props.auth.user.role === 'admin'"
                                    :href="route('admin.categories.index')"
                                    :active="route().current('admin.categories.*')"
                                >
                                    {{ t('categories') }}
                                </NavLink>
                                <NavLink
                                    v-if="$page.props.auth.user.role === 'admin'"
                                    :href="route('admin.products.index')"
                                    :active="route().current('admin.products.*')"
                                >
                                    {{ t('products') }}
                                </NavLink>
                                <NavLink
                                    v-if="$page.props.auth.user.role === 'admin'"
                                    :href="route('admin.tables.index')"
                                    :active="route().current('admin.tables.*')"
                                >
                                    {{ t('tables_qr') }}
                                </NavLink>
                                <NavLink
                                    v-if="['admin', 'cashier'].includes($page.props.auth.user.role)"
                                    :href="route('admin.cashier.index')"
                                    :active="route().current('admin.cashier.*')"
                                >
                                    {{ t('cashier') }}
                                </NavLink>
                                <NavLink
                                    v-if="['admin', 'kitchen'].includes($page.props.auth.user.role)"
                                    :href="route('admin.kitchen.index')"
                                    :active="route().current('admin.kitchen.*')"
                                >
                                    {{ t('kitchen') }}
                                </NavLink>
                                <NavLink
                                    v-if="$page.props.auth.user.role === 'admin'"
                                    :href="route('admin.reports.index')"
                                    :active="route().current('admin.reports.*')"
                                >
                                    {{ t('reports') }}
                                </NavLink>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            <!-- Language Switcher -->
                            <div class="flex space-x-2 mr-4 border-r pr-4 border-gray-300">
                                <button @click="changeLanguage('th')" :class="locale === 'th' ? 'font-bold text-indigo-600' : 'text-gray-500 hover:text-gray-700'">TH</button>
                                <button @click="changeLanguage('vi')" :class="locale === 'vi' ? 'font-bold text-indigo-600' : 'text-gray-500 hover:text-gray-700'">VN</button>
                                <button @click="changeLanguage('en')" :class="locale === 'en' ? 'font-bold text-indigo-600' : 'text-gray-500 hover:text-gray-700'">EN</button>
                                <button @click="changeLanguage('lo')" :class="locale === 'lo' ? 'font-bold text-indigo-600' : 'text-gray-500 hover:text-gray-700'">LA</button>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button
                                                type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                            >
                                                {{ $page.props.auth.user.name }}

                                                <svg
                                                    class="-me-0.5 ms-2 h-4 w-4"
                                                    xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20"
                                                    fill="currentColor"
                                                >
                                                    <path
                                                        fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd"
                                                    />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

                                    <template #content>
                                        <DropdownLink
                                            :href="route('profile.edit')"
                                        >
                                            {{ t('profile') }}
                                        </DropdownLink>
                                        <DropdownLink
                                            :href="route('logout')"
                                            method="post"
                                            as="button"
                                        >
                                            {{ t('log_out') }}
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger -->
                        <div class="-me-2 flex items-center sm:hidden">
                            <button
                                @click="
                                    showingNavigationDropdown =
                                        !showingNavigationDropdown
                                "
                                class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none"
                            >
                                <svg
                                    class="h-6 w-6"
                                    stroke="currentColor"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
                                        }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Responsive Navigation Menu -->
                <div
                    :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }"
                    class="sm:hidden"
                >
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            {{ t('dashboard') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="$page.props.auth.user.role === 'admin'"
                            :href="route('admin.categories.index')"
                            :active="route().current('admin.categories.*')"
                        >
                            {{ t('categories') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="$page.props.auth.user.role === 'admin'"
                            :href="route('admin.products.index')"
                            :active="route().current('admin.products.*')"
                        >
                            {{ t('products') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="$page.props.auth.user.role === 'admin'"
                            :href="route('admin.tables.index')"
                            :active="route().current('admin.tables.*')"
                        >
                            {{ t('tables_qr') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="['admin', 'cashier'].includes($page.props.auth.user.role)"
                            :href="route('admin.cashier.index')"
                            :active="route().current('admin.cashier.*')"
                        >
                            {{ t('cashier') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="['admin', 'kitchen'].includes($page.props.auth.user.role)"
                            :href="route('admin.kitchen.index')"
                            :active="route().current('admin.kitchen.*')"
                        >
                            {{ t('kitchen') }}
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            v-if="$page.props.auth.user.role === 'admin'"
                            :href="route('admin.reports.index')"
                            :active="route().current('admin.reports.*')"
                        >
                            {{ t('reports') }}
                        </ResponsiveNavLink>
                    </div>

                    <!-- Responsive Settings Options -->
                    <div
                        class="border-t border-gray-200 pb-1 pt-4"
                    >
                        <div class="px-4">
                            <div
                                class="text-base font-medium text-gray-800"
                            >
                                {{ $page.props.auth.user.name }}
                            </div>
                            <div class="text-sm font-medium text-gray-500">
                                {{ $page.props.auth.user.email }}
                            </div>
                        </div>

                        <!-- Mobile Language Switcher -->
                        <div class="px-4 mt-3 flex space-x-3 border-t pt-3 border-gray-200">
                            <button @click="changeLanguage('th')" :class="locale === 'th' ? 'font-bold text-indigo-600' : 'text-gray-500'">TH</button>
                            <button @click="changeLanguage('vi')" :class="locale === 'vi' ? 'font-bold text-indigo-600' : 'text-gray-500'">VN</button>
                            <button @click="changeLanguage('en')" :class="locale === 'en' ? 'font-bold text-indigo-600' : 'text-gray-500'">EN</button>
                            <button @click="changeLanguage('lo')" :class="locale === 'lo' ? 'font-bold text-indigo-600' : 'text-gray-500'">LA</button>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">
                                {{ t('profile') }}
                            </ResponsiveNavLink>
                            <ResponsiveNavLink
                                :href="route('logout')"
                                method="post"
                                as="button"
                            >
                                {{ t('log_out') }}
                            </ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <!-- Page Heading -->
            <header
                class="bg-white shadow"
                v-if="$slots.header"
            >
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main>
                <slot />
            </main>
        </div>
    </div>
</template>

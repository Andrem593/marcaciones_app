<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import Welcome from '@/Components/Welcome.vue';
import Reporte from '@/Pages/Reports/reporte.vue';
</script>

<template>
    <AppLayout title="Inicio">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Sistema de Control de Marcaciones
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 sm:px-20 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <div>
                            <h1 class="text-2xl font-semibold text-gray-800 dark:text-gray-200">Marcaciones</h1>
                        </div>
                    </div>
                    <div class="p-6 sm:px-20 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <div class="mt-4 text-gray-500 dark:text-gray-300">
                            <Reporte />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

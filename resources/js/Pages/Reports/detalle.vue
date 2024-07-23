<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { defineProps, ref, onMounted } from 'vue';
const props = defineProps({
    empleado: {
        type: String,
        required: true
    },
    fecha: {
        type: String,
        required: true
    }
});

const marcaciones = ref([]);

// Función para obtener las marcaciones del empleado
const fetchMarcaciones = async () => {
    try {
        const response = await axios.get(`/api/marcaciones/${props.empleado}/${props.fecha}`);
        marcaciones.value = response.data;
    } catch (error) {
        console.error('Error fetching marcaciones:', error);
    }
};

// Carga las marcaciones cuando el componente se monta
onMounted(() => {
    fetchMarcaciones();
});

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
                            <div class="overflow-x-auto">
                                <table class="min-w-full bg-white shadow-md rounded-lg">
                                    <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                                        <tr>
                                            <th class="py-3 px-6 text-left">ID</th>
                                            <th class="py-3 px-6 text-left">Empleado</th>
                                            <th class="py-3 px-6 text-left">Fecha</th>
                                            <th class="py-3 px-6 text-left">Hora</th>
                                            <th class="py-3 px-6 text-left">Briometrico</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 text-sm font-light">
                                        <tr v-for="item in marcaciones" :key="item.id"
                                            class="border-b border-gray-200 hover:bg-gray-100">
                                            <td class="py-3 px-6 text-left">{{ item.empleado_id }}</td>
                                            <td class="py-3 px-6 text-left">{{ item.empleado }}</td>
                                            <td class="py-3 px-6 text-left">{{ item.fecha }}</td>
                                            <td class="py-3 px-6 text-left">{{ item.hora }}</td>
                                            <td class="py-3 px-6 text-left">{{ item.biometrico }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

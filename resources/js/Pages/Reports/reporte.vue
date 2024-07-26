<template>
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-4">
            <div>
                <input v-model="searchQuery" type="text" placeholder="Buscar"
                    class="border border-gray-300 rounded-lg p-2 w-1/4" />
                <input v-model="filterStartDate" type="date" placeholder="Fecha de inicio"
                    class="border border-gray-300 rounded-lg p-2 ml-2" />
                <input v-model="filterEndDate" type="date" placeholder="Fecha de fin"
                    class="border border-gray-300 rounded-lg p-2 ml-2" />
            </div>
            <div>
                <button @click="filterData"
                    class="bg-blue-500 text-white rounded-lg px-4 py-2 ml-2 hover:bg-blue-600">Buscar</button>
                <button @click="exportData"
                    class="bg-green-500 text-white rounded-lg px-4 py-2 ml-2 hover:bg-green-600">Exportar</button>
            </div>

        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">ID</th>
                        <th class="py-3 px-6 text-left">Empleado</th>
                        <th class="py-3 px-6 text-left">Fecha</th>
                        <th class="py-3 px-6 text-left">Entrada</th>
                        <th class="py-3 px-6 text-left">S.Almuerzo</th>
                        <th class="py-3 px-6 text-left">E.Almuerzo</th>
                        <th class="py-3 px-6 text-left">Salida</th>
                        <th class="py-3 px-6 text-left">Briometrico</th>
                        <th class="py-3 px-6 text-left">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <tr v-for="item in filteredData" :key="item.id" class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ item.empleado_id }}</td>
                        <td class="py-3 px-6 text-left">{{ item.empleado }}</td>
                        <td class="py-3 px-6 text-left">{{ item.fecha }}</td>
                        <td class="py-3 px-6 text-left">{{ item.entrada }}</td>
                        <td class="py-3 px-6 text-left">{{ item.salida_almuerzo }}</td>
                        <td class="py-3 px-6 text-left">{{ item.entrada_almuerzo }}</td>
                        <td class="py-3 px-6 text-left">{{ item.salida }}</td>
                        <td class="py-3 px-6 text-left">{{ item.biometrico }}</td>
                        <td class="py-3 px-6 text-left">
                            <button @click="viewDetails(item.empleado_id, item.fecha)"
                                class="bg-blue-500 text-white rounded-lg px-4 py-2 hover:bg-blue-600">Ver</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { Inertia } from '@inertiajs/inertia';

export default {
    data() {
        return {
            filterStartDate: '',
            filterEndDate: '',
            searchQuery: '',
            data: [],
            filteredData: []
        }
    },

    methods: {
        async filterData() {
            // Realiza una solicitud a la API con los parámetros proporcionados
            const params = {
                empleado: this.searchQuery, // Usamos searchQuery como empleado si corresponde
                inicio: this.filterStartDate,
                fin: this.filterEndDate
            };

            try {
                const response = await axios.get('/api/marcaciones', { params });
                this.data = response.data;
                this.filteredData = this.data; // Actualizamos filteredData con los datos obtenidos
            } catch (error) {
                console.log(error);
            }
        },
        async getData() {
            await axios.get('/api/marcaciones').then(response => {
                this.data = response.data;
            }).catch(error => {
                console.log(error);
            });
        },
        async exportData() {
            const params = {
                empleado: this.searchQuery, // Usamos searchQuery como empleado si corresponde
                inicio: this.filterStartDate,
                fin: this.filterEndDate
            };
            await axios.get('/api/marcaciones/export', { params }).then(response => {
                // Descarga el archivo CSV
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'reporte_marcaciones.csv');
                document.body.appendChild(link);
                link.click();
            }).catch(error => {
                
            });
        },
        viewDetails(empleado_id, fecha) {
            Inertia.visit(`/Reporte/detalle/${empleado_id}/${fecha}`);
        }
    },

    async mounted() {
        await this.getData();
        this.filteredData = this.data;
    }
};
</script>

<style scoped>
/* Agrega estilos personalizados si es necesario */
</style>
<template>
    <div class="container mx-auto p-4">
        <div class="flex justify-between items-center mb-4">
            <div>
                <input v-model="searchQuery" @input="filterData" type="text" placeholder="Buscar"
                    class="border border-gray-300 rounded-lg p-2 w-1/4" />
                <input v-model="filterStartDate" @input="filterData" type="date" placeholder="Fecha de inicio"
                    class="border border-gray-300 rounded-lg p-2 ml-2" />
                <input v-model="filterEndDate" @input="filterData" type="date" placeholder="Fecha de fin"
                    class="border border-gray-300 rounded-lg p-2 ml-2" />
            </div>
            <button @click="filterData"
                class="bg-blue-500 text-white rounded-lg px-4 py-2 ml-2 hover:bg-blue-600">Buscar</button>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white shadow-md rounded-lg">
                <thead class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <tr>
                        <th class="py-3 px-6 text-left">ID</th>
                        <th class="py-3 px-6 text-left">Nombre</th>
                        <th class="py-3 px-6 text-left">Fecha</th>
                        <th class="py-3 px-6 text-left">Entrada</th>
                        <th class="py-3 px-6 text-left">Salida</th>
                        <th class="py-3 px-6 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    <tr v-for="item in filteredData" :key="item.id" class="border-b border-gray-200 hover:bg-gray-100">
                        <td class="py-3 px-6 text-left">{{ item.id }}</td>
                        <td class="py-3 px-6 text-left">{{ item.name }}</td>
                        <td class="py-3 px-6 text-left">{{ item.date }}</td>
                        <td class="py-3 px-6 text-left">{{ item.timeIn }}</td>
                        <td class="py-3 px-6 text-left">{{ item.timeOut }}</td>
                        <td class="py-3 px-6 text-center">
                            <button
                                class="bg-yellow-500 text-white rounded-lg px-2 py-1 mr-2 hover:bg-yellow-600">Editar</button>
                            <button
                                class="bg-red-500 text-white rounded-lg px-2 py-1 hover:bg-red-600">Eliminar</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            filterStartDate: '',
            filterEndDate: '',
            searchQuery: '',
            data: [
                { id: 1, name: 'John Doe', date: '2021-10-01', timeIn: '08:00:00', timeOut: '17:00:00' },
                { id: 2, name: 'Jane Doe', date: '2021-10-01', timeIn: '08:00:00', timeOut: '17:00:00' },
                { id: 3, name: 'John Smith', date: '2021-10-01', timeIn: '08:00:00', timeOut: '17:00:00' },
                { id: 4, name: 'Jane Smith', date: '2021-10-01', timeIn: '08:00:00', timeOut: '17:00:00' },
                { id: 5, name: 'John Johnson', date: '2021-10-01', timeIn: '08:00:00', timeOut: '17:00:00' },
                { id: 6, name: 'Jane Johnson', date: '2021-10-01', timeIn: '08:00:00', timeOut: '17:00:00' },
                { id: 7, name: 'John Brown', date: '2021-10-01', timeIn: '08:00:00', timeOut: '17:00:00' },
                { id: 8, name: 'Jane Brown', date: '2021-10-01', timeIn: '08:00:00', timeOut: '17:00:00' },
                { id: 9, name: 'John White', date: '2021-10-01', timeIn: '08:00:00', timeOut: '17:00:00' },
                { id: 10, name: 'Jane White', date: '2021-10-01', timeIn: '08:00:00', timeOut: '17:00:00' },

            ],
            filteredData: []
        }
    },

    methods: {
        filterData() {
            this.filteredData = this.items.filter(item => {
                const matchesQuery = item.name.toLowerCase().includes(this.searchQuery.toLowerCase()) || item.id.toString().includes(this.searchQuery);
                const matchesDateRange = (!this.filterStartDate || new Date(item.date) >= new Date(this.filterStartDate)) &&
                    (!this.filterEndDate || new Date(item.date) <= new Date(this.filterEndDate));

                return matchesQuery && matchesDateRange;
            });
        }
    },

    mounted() {
        this.filteredData = this.data;
    }
};
</script>

<style scoped>
/* Agrega estilos personalizados si es necesario */
</style>
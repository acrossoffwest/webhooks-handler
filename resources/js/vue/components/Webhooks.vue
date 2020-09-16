<template>
    <div>
        <data-table
            :columns="columns"
            url="/api/webhooks"
        />
    </div>
</template>

<script>
    export default {
        name: "Webhooks",
        data() {
            return {
                columns: [
                    {
                        label: 'ID',
                        name: 'id',
                        orderable: true,
                    },
                    {
                        label: 'Name',
                        name: 'name',
                        orderable: true,
                    },
                    {
                        label: 'In',
                        name: 'in',
                        orderable: true,
                    },
                    {
                        label: 'Out',
                        name: 'out',
                        orderable: true,
                    }
                ]
            }
        },
        computed: {
            authorized () {
                return !!axios.defaults.headers.common['Authorization']
            },
            headers () {
                return {
                    Authorization: window.axios.defaults.headers.common['Authorization']
                }
            }
        }
    }
</script>

<style scoped>

</style>

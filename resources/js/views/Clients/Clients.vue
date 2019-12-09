<template>
    <div>
      <clients-table title="Клиенты"/>
    </div>
</template>

<script>
    import ClientsTable from "../../components/Tables/ClientsTable";
    import ACTIONS from "../../store/actions";
    export default {
        mounted() {
            this.$store.dispatch(ACTIONS.GET_CLIENTS);
        },
        components: {
            ClientsTable,
        },
        data: () => ({
        })
    }
</script>

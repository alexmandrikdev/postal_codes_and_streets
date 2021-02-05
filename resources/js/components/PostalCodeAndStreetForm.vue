<template>
    <div>
        <h4 class="text-center text-dark">Add new Postal Code or Street</h4>
        <b-form
            inline
            class="justify-content-center mt-3"
            @submit.prevent="add"
        >
            <b-form-select
                v-model="type"
                class="mx-2"
                :options="typeOptions"
                required
            />

            <b-form-input
                v-model="name"
                class="mx-2"
                :placeholder="nameInputPlaceholder"
                required
            />

            <b-form-input
                v-model="latitude"
                class="mx-2"
                placeholder="Latitude"
                required
            />

            <b-form-input
                v-model="longitude"
                class="mx-2"
                placeholder="Longitude"
                required
            />

            <b-button class="mx-2" type="submit" variant="primary"
                >Add</b-button
            >
        </b-form>
    </div>
</template>

<script>
export default {
    data() {
        return {
            type: null,
            typeOptions: [
                { value: null, text: 'Type', disabled: true },
                { value: 'postalCode', text: 'Postal Code' },
                { value: 'street', text: 'Street' },
            ],
            name: '',
            latitude: '',
            longitude: '',
        };
    },
    computed: {
        nameInputPlaceholder() {
            return this.type === 'postalCode'
                ? 'Postal Code'
                : this.type === 'street'
                ? 'Street'
                : 'Postal Code or Street';
        },
    },
    methods: {
        add() {},
    },
};
</script>

<style scoped></style>

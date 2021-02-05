<template>
    <b-container>
        <postal-code-and-street-form class="my-4" />

        <div class="w-100 border-top border-info"></div>

        <search class="my-5" />
    </b-container>
</template>

<script>
import PostalCodeAndStreetForm from '../components/PostalCodeAndStreetForm.vue';
import Search from '../components/Search.vue';

export default {
    components: { PostalCodeAndStreetForm, Search },
};
</script>

<style scoped></style>

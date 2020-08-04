<template>
    <div class="container">
        <form @submit.prevent="formSubmit">
            <div class="form-group">
                <label for="idText">Id</label>
                <input
                    v-model="id"
                    type="text"
                    class="form-control"
                    id="idText"
                    placeholder="Enter Id Here"
                />
            </div>
            <div class="form-group">
                <label for="titleText">Title</label>
                <input
                    v-model="title"
                    type="text"
                    class="form-control"
                    id="titleText"
                    placeholder="Enter Title Here"
                />
            </div>
            <div class="form-group">
                <label for="contentText">Enter Content Here</label>
                <textarea
                    class="form-control"
                    id="contentText"
                    v-model="content"
                    rows="3"
                ></textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                Submit
            </button>
        </form>
    </div>
</template>

<script>
import axios from "axios";
export default {
    mounted() {
        console.log("Component mounted.");
    },
    data() {
        return {
            id: "",
            title: "",
            content: ""
        };
    },
    methods: {
        formSubmit(e) {
            axios
                .post("/store", {
                    id: this.id,
                    title: this.title,
                    content: this.content
                })
                .then(function(response) {
                    currentObj.output = response.data;
                })
                .catch(function(error) {
                    currentObj.output = error;
                });
        }
    }
};
</script>
<style lang="stylus" scoped></style>

<template>
    <div>
        <nav>
            <ul class="pagination">
                <li class="page-item" :class="current_page == 1 ? 'disabled' : ''">
                    <a class="page-link" :href="prev_page_url" tabindex="-1" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                        <span class="sr-only">Previous</span>
                    </a>
                </li>

                <li v-for="number in last_page" class="page-item" :class="current_page == number ? 'active' : ''"
                    v-show="isVisible(number)">
                    <a class="page-link" @click.prevent="updateRecords" :href="url_path + 'page=' + number">{{ number
                        }}</a>
                </li>

                <li class="page-item" :class="current_page == last_page ? 'disabled' : ''">
                    <a class="page-link" :href="next_page_url" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                        <span class="sr-only">Next</span>
                    </a>
                </li>
            </ul>
            <p>Showing {{ name }} {{ from }} to {{ to }} out of {{ total }}
            <p>
        </nav>
    </div>
</template>


<script>

    export default {
        props: ["current_page", "prev_page_url", "last_page", "next_page_url", "url_path", "total", "name", "per_page", "updateRecords"],

        data(){
            return{

            }
        },

        methods: {
            isVisible(page){
                if((page - this.current_page <= 2 && page - this.current_page >= -2) || page == this.last_page || page == 1)
                    return true;

                return false;
            }
        },
        computed : {
            from: function () {
                if (this.current_page == 1 && this.total != 0) {
                    return this.from = 1;
                }
                if(this.total == 0) {
                    return this.from = 0;
                }

                return this.from = this.per_page * (this.current_page - 1) + 1
            },

            to: function () {
                 if (this.current_page == 1 && this.total > this.per_page) {
                    return this.to = this.per_page;
                }
                if(this.total < this.per_page) {
                    return this.to = this.total;
                }
                return this.to = this.per_page * this.current_page;
            }
        }

    }

</script>
<style>
    .pagination .page-item a {
        display: block;
    }
</style>
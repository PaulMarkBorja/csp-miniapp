<template>
    <v-app id="inspire">
        <v-navigation-drawer v-model="drawer" app expand-on-hover>
            <v-list dense>
                <v-list-item link @click="logout">
                    <v-list-item-action>
                        <v-icon>mdi-power</v-icon>
                    </v-list-item-action>
                    <v-list-item-content>
                        <v-list-item-title>Logout</v-list-item-title>
                    </v-list-item-content>
                </v-list-item>
            </v-list>
        </v-navigation-drawer>

        <v-app-bar app color="blue" dark>
            <v-app-bar-nav-icon @click.stop="drawer = !drawer" />
            <v-toolbar-title>Songs & Artists</v-toolbar-title>
        </v-app-bar>

        <v-content>
            <v-container class="fill-height" fluid>
                <v-row align="center" justify="center">
                    <v-col class="text-center">
                        <template>
                            <v-card
                                class="mx-auto elevation-20"
                                color="#E9F1F1"
                                light
                                style="max-width: 1000px;"
                            >
                                <template>
                                    <v-data-table
                                        :headers="headers"
                                        :items="items"
                                        sort-by="rating"
                                        class="elevation-1"
                                    >
                                        <template v-slot:top>
                                            <v-toolbar flat color="white">
                                                <v-toolbar-title
                                                    >Artists</v-toolbar-title
                                                >
                                                <v-divider
                                                    class="mx-4"
                                                    inset
                                                    vertical
                                                ></v-divider>
                                                <v-spacer></v-spacer>
                                                <v-dialog
                                                    v-model="dialog"
                                                    max-width="500px"
                                                >
                                                    <template
                                                        v-slot:activator="{
                                                            on
                                                        }"
                                                    >
                                                        <div
                                                            class="text-center"
                                                        >
                                                            <v-btn
                                                                rounded
                                                                color="primary"
                                                                dark
                                                                v-on="on"
                                                            >
                                                                <v-icon left
                                                                    >mdi-plus</v-icon
                                                                >Add new artist
                                                            </v-btn>
                                                        </div>
                                                    </template>

                                                    <!--modal for adding new artist-->
                                                    <v-card>
                                                        <v-card-title>
                                                            <span
                                                                class="headline"
                                                            >
                                                                {{ formTitle }}
                                                            </span>
                                                        </v-card-title>

                                                        <v-card-text>
                                                            <v-container fluid>
                                                                <v-row>
                                                                    <v-col
                                                                        cols="12"
                                                                    >
                                                                        <v-text-field
                                                                            v-model="
                                                                                editedItem.name
                                                                            "
                                                                            label="Name"
                                                                            counter
                                                                            filled
                                                                            :rules="
                                                                                rules
                                                                            "
                                                                            :value="
                                                                                value
                                                                            "
                                                                            shaped
                                                                            height="30"
                                                                        ></v-text-field>
                                                                        <v-textarea
                                                                            v-model="
                                                                                editedItem.description
                                                                            "
                                                                            label="Description"
                                                                            counter
                                                                            filled
                                                                            :rules="
                                                                                rules
                                                                            "
                                                                            :value="
                                                                                value
                                                                            "
                                                                            shaped
                                                                            height="30"
                                                                        ></v-textarea>
                                                                    </v-col>
                                                                </v-row>
                                                            </v-container>
                                                        </v-card-text>

                                                        <v-card-actions>
                                                            <v-spacer></v-spacer>
                                                            <v-btn
                                                                color="blue darken-1"
                                                                text
                                                                @click="close"
                                                                >Cancel</v-btn
                                                            >
                                                            <v-btn
                                                                color="blue darken-1"
                                                                text
                                                                @click="save"
                                                                >Save</v-btn
                                                            >
                                                        </v-card-actions>
                                                    </v-card>
                                                </v-dialog>
                                            </v-toolbar>
                                        </template>
                                        <template v-slot:item.action="{ item }">
                                            <v-icon
                                                small
                                                class="mr-2"
                                                @click="editItem(item)"
                                                >mdi-pencil</v-icon
                                            >
                                            <v-icon
                                                small
                                                @click="deleteItem(item)"
                                                >mdi-trash-can-outline</v-icon
                                            >
                                        </template>
                                        <template v-slot:no-data>
                                            <v-btn
                                                color="primary"
                                                @click="initialize"
                                                >Reset</v-btn
                                            >
                                        </template>
                                    </v-data-table>
                                </template>
                            </v-card>
                        </template>
                    </v-col>
                </v-row>
            </v-container>
        </v-content>

        <v-footer color="blue" app>
            <span class="white--text">CSP &copy; 2020</span>
        </v-footer>
    </v-app>
</template>

<script>
export default {
    props: {
        source: String
    },
    data: () => ({
        drawer: null,
        rules: [v => v.length <= 100 || "Max 25 characters"],
        value: "",


        dialog: false,
        headers: [
            {
                text: "Name",
                align: "left",
                sortable: false,
                value: "name"
            },
            { text: "Description", value: "description", sortable: false },
            { text: "Actions", value: "action", sortable: false }
        ],
        items: [],
        editedIndex: -1,
        editedItem: {
            id: "",
            name: "",
            description: ""
        },
        defaultItem: {
            name: "",
            description: ""
        }
    }),

    computed: {
        formTitle() {
            return this.editedIndex === -1 ? "Add new artist" : "Edit artist information";
        }
    },

    watch: {
        dialog(val) {
            val || this.close();
        }
    },

    created() {
        this.initialize();
    },

    methods: {
        initialize() {
            axios.get("/api/artists").then(res => {
                this.items = res.data;
            });
        },

        editItem(item) {
            this.editedIndex = this.items.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },

        deleteItem(item) {
            const index = this.items.indexOf(item);
            confirm("Are you sure you want to delete this item?") &&
                this.items.splice(index, 1) &&
                axios.delete("/api/artists/" + item.id);
        },

        close() {
            this.dialog = false;
            setTimeout(() => {
                this.editedItem = Object.assign({}, this.defaultItem);
                this.editedIndex = -1;
            }, 300);
        },

        save() {
            if (this.editedIndex > -1) {
                Object.assign(this.items[this.editedIndex], this.editedItem);

                axios
                    .patch(
                        "/api/artists/" + this.editedItem.id,
                        this.editedItem
                    )
                    .then(res => {
                        return res.data;
                    });
            } else {
                axios.post("/api/artists", this.editedItem).then(res => {
                    return res.data;
                });
                this.items.push(this.editedItem);
            }
            this.close();
        },

        logout() {
            axios.post("./logout").then(response => {
                window.location.href = "./";
            });
        }
    }
};
</script>

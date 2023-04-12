<template>
    <div class="container my-5">
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header">
                        Cadastrar Cliente
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="name" class="form-label">Nome:</label>
                                <input type="text" class="form-control" id="name" v-model="newCustomer.name">
                            </div>
                            <div class="col-md-3">
                                <label for="cpf" class="form-label">CPF:</label>
                                <input type="text" class="form-control" id="cpf" v-model="newCustomer.cpf">
                            </div>
                            <div class="col-md-3">
                                <label for="birthdate" class="form-label">Data Nasc.:</label>
                                <input type="date" class="form-control" id="birthdate" v-model="newCustomer.birthdate">
                            </div>
                            <div class="col-md-3">
                                <label for="gender" class="form-label">Sexo:</label>
                                <select class="form-select" id="gender" v-model="newCustomer.gender">
                                    <option selected disabled>Selecione...</option>
                                    <option v-for="gender in genders" :key="gender.id" :value="gender.abbreviation">{{ gender.name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-3">
                                <label for="address" class="form-label">Endereço:</label>
                                <input type="text" class="form-control" id="address" v-model="newCustomer.address.address">
                            </div>
                            <div class="col-md-4">
                                <label for="state" class="form-label">Estado:</label>
                                <select class="form-select" id="state" v-model="newCustomer.address.state">
                                    <option selected disabled>Selecione...</option>
                                    <option v-for="state in states" :key="state.id" :value="state.acronym">{{ state.name }}</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="city" class="form-label">Cidade:</label>
                                <input type="text" class="form-control" id="city" v-model="newCustomer.address.city">
                            </div>
                        </div>
                        <button class="btn btn-primary mt-3" @click="saveNewCustomer()">Salvar</button>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        Lista de Clientes
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Nome</th>
                                        <th scope="col">CPF</th>
                                        <th scope="col">Data Nasc.</th>
                                        <th scope="col">Estado</th>
                                        <th scope="col">Cidade</th>
                                        <th scope="col">Sexo</th>
                                        <th scope="col">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="customer in customers" :key="customer.id">
                                        <th>{{ customer.id }}</th>
                                        <td>{{ customer.name }}</td>
                                        <td>{{ formatCpf(customer.cpf) }}</td>
                                        <td>{{ new Date(customer.bithdate).toLocaleDateString() }}</td>
                                        <td>{{ customer.address.state }}</td>
                                        <td>{{ customer.address.city }}</td>
                                        <td>{{ customer.gender }}</td>
                                        <td><button class="btn btn-danger btn-sm" @click="deleteCustomer(customer.id)">Excluir</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div> 
                        <nav aria-label="Page navigation example">
                            <ul class="pagination">
                                <li v-for="link, key in links.links" :key="key" class="page-item" :class="(link.url == null ? 'disabled' : '') + (link.active ? 'active' : '')"><a class="page-link" :href="link.url ?? '#'" v-html="link.label"></a></li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

    export default {
        data() {
            return {
                baseUrl: window.location.origin + '/api',
                states: [],
                genders: [],
                customers: [],
                links: [],
                newCustomer: {
                    name: undefined,
                    cpf: '',
                    birthdate: '',
                    address: {
                        address: '',
                        state: undefined,
                        city: '',
                    },
                    gender: undefined,
                }
            }
        },
        methods: {
            formatCpf(cpf) {
                return cpf.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4');
            },
            setStates() {
                axios.get(this.baseUrl + '/states')
                    .then(response => {
                        this.states = response.data.data;
                    })
                    .catch(error => {
                        console.log(error);
                        alert('Erro ao carregar estados');
                    });
            },
            setGenders() {
                axios.get(this.baseUrl + '/genders')
                    .then(response => {
                        this.genders = response.data.data;
                    })
                    .catch(error => {
                        console.log(error);
                        alert('Erro ao carregar gêneros');
                    });
            },
            setCustomers() {
                axios.get(this.baseUrl + '/customers')
                    .then(response => {
                        this.customers = response.data.data;
                        this.links = response.data.meta;
                        console.log(response.data)
                    })
                    .catch(error => {
                        console.log(error);
                        alert('Erro ao carregar clientes');
                    });
            },
            deleteCustomer(id) {
                axios.delete(this.baseUrl + '/customers/' + id)
                    .then(response => {
                        this.setCustomers();
                        alert('Cliente excluído com sucesso!')
                    })
                    .catch(error => {
                        console.log(error);
                        alert('Erro ao excluir cliente');
                    });
            },
            saveNewCustomer() {
                this.newCustomer.cpf = this.newCustomer.cpf.replace(/\D/g, '');
                axios.post(this.baseUrl + "/customers", this.newCustomer)
                .then(response => {
                    this.setCustomers();
                    this.resetNewCustomer();
                    alert('Cliente cadastrado com sucesso!')
                })
                .catch(error => {
                    const errors = error.response.data.errors;
                    for (const err in errors) {
                        for (let errorMessage in errors[err]) {
                            alert(errors[err][errorMessage]);
                        }
                    }
                })
            },
            resetNewCustomer() {
                this.newCustomer =  {
                    name: '',
                    cpf: '',
                    birthdate: '',
                    address: {
                        address: '',
                        state: '',
                        city: '',
                    },
                    gender: '',
                }
            }
        },
        mounted() {
            this.setStates();
            this.setGenders();
            this.setCustomers();
        },
    }
</script>
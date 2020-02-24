<template>
    <v-app>

        <v-app-bar
            app
            color="indigo"
            dark>
            <v-app-bar-nav-icon />
            <v-toolbar-title>Keyword</v-toolbar-title>
        </v-app-bar>

        <v-content>
            <v-container fluid>
                <v-row>
                    <v-col cols="12">
                        <v-card>
                            <v-card-title>
                                Nhập từ khóa từ file excel
                            </v-card-title>
                            <v-card-text>
                                <v-form v-model="formValid2">
                                    <v-row>
                                        <v-col cols="3">
                                            <input accept=".csv" id="file" type="file" @change="onChange">
                                        </v-col>
                                        <v-col cols="3">
                                            <v-text-field
                                                    label="Type"
                                                    outlined
                                                    dense
                                                    v-model="type"
                                                    :rules="[() => type <= 3 && type >= 1 || 'Nhập 1,2 hoặc 3']"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="3">
                                            <v-text-field
                                                    label="Cú pháp"
                                                    outlined
                                                    dense
                                                    v-model="syntax"
                                                    :rules="[() => syntax <= 3 && syntax >= 1 || 'Nhập 1,2 hoặc 3']"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="3">
                                            <v-btn :disabled="!formValid2"  @click="importExcel">upload</v-btn>
                                        </v-col>
                                    </v-row>
                                </v-form>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>

                <v-row>
                    <v-col cols="12">
                        <v-card>
                            <v-card-title>
                                Chạy Keyword planner
                            </v-card-title>
                            <v-card-text>
                                <v-form v-model="formValid">
                                    <v-row>
                                        <v-col cols="4">
                                            <v-text-field
                                                v-model="stream"
                                                label="Nhập số luồng 1-10"
                                                :rules="[() => stream <= 10 && stream >= 1 || 'Tối đa 10 luồng']"
                                                outlined
                                                dense
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="4">
                                            <v-btn :disabled="!formValid" @click="setStream">ok</v-btn>
                                        </v-col>
                                    </v-row>
                                </v-form>
                            </v-card-text>

                            <v-card-text>
                                <v-row>
                                    <v-col v-for="(item,index) in info_stream"  cols="4">
                                        <p class="text-primary">
                                            Luồng {{index+1}} : {{item.page_from}} -> {{item.page_to}}
                                        </p>
                                    </v-col>
                                </v-row>
                            </v-card-text>
                        </v-card>

                    </v-col>
                </v-row>



                <v-row>
                    <v-col cols="12">
                        <v-card>
                            <v-card-title>
                                Chạy Search Result
                            </v-card-title>
                            <v-card-text>
                                <v-form v-model="formValid3">
                                    <v-row>
                                        <v-col cols="4">
                                            <v-text-field
                                                v-model="stream_search_result"
                                                label="Nhập số luồng 1-10"
                                                :rules="[() => stream_search_result <= 10 && stream_search_result >= 1 || 'Tối đa 10 luồng']"
                                                outlined
                                                dense
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="4">
                                            <v-btn :disabled="!formValid3" @click="setStreamSearchResult">ok</v-btn>
                                        </v-col>
                                    </v-row>
                                </v-form>
                            </v-card-text>

                            <v-card-text>
                                <v-row>
                                    <v-col v-for="(item,index) in info_streamSR"  cols="4">
                                        <p class="text-primary">
                                            Luồng {{index+1}} : {{item.index_from}} -> {{item.index_to}}
                                        </p>
                                    </v-col>
                                </v-row>
                            </v-card-text>

                        </v-card>

                    </v-col>
                </v-row>


                <v-row>
                    <v-col cols="12">
                        <v-card>
                            <v-card-title>
                                Setting
                            </v-card-title>
                            <v-card-text>
                                <v-subheader>Tài khoản login google</v-subheader>
                                <v-form >
                                    <v-row>
                                        <v-col cols="3">
                                            <v-text-field
                                                    label="Email"
                                                    outlined
                                                    dense
                                                    v-model="email"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="3">
                                            <v-text-field
                                                    label="Password"
                                                    outlined
                                                    dense
                                                    v-model="pass"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="3">
                                            <v-text-field
                                                    label="User link"
                                                    outlined
                                                    dense
                                                    v-model="user_link"
                                            ></v-text-field>
                                        </v-col>

                                        <v-col cols="3">
                                            <v-btn @click="saveGoogle">Lưu</v-btn>

                                            <v-btn class="ml-3" @click="loginGG">Login GG</v-btn>
                                        </v-col>
                                    </v-row>
                                </v-form>
                            </v-card-text>

                            <v-card-text>
                                <v-subheader>API captcha google</v-subheader>
                                <v-form >
                                    <v-row>
                                        <v-col cols="3">
                                            <v-text-field
                                                    label="API Key"
                                                    outlined
                                                    dense
                                                    v-model="api_key"
                                            ></v-text-field>
                                        </v-col>
                                        <v-col cols="3">
                                            <v-text-field
                                                    label="Site key"
                                                    outlined
                                                    dense
                                                    v-model="site_key"
                                            ></v-text-field>
                                        </v-col>

                                        <v-col cols="3">
                                            <v-btn @click="saveCaptcha">Lưu</v-btn>
                                        </v-col>
                                    </v-row>
                                </v-form>
                            </v-card-text>

                            <v-card-text>
                                <v-subheader>Proxy</v-subheader>
                                <v-form >
                                    <v-row>
                                        <v-col cols="6">
                                            <v-textarea
                                                    outlined
                                                    name="input-7-4"
                                                    label="Nhập danh sách proxy cách nhau dấu ,"
                                                    v-model="proxy"
                                            ></v-textarea>
                                        </v-col>

                                        <v-col cols="3">
                                            <v-btn @click="saveProxy">Lưu</v-btn>
                                        </v-col>
                                    </v-row>
                                </v-form>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-content>

    </v-app>
</template>

<script>
    export default {
        data()
        {
            return{
                file:null,
                formValid: true,
                formValid2: true,
                formValid3: true,
                type:'',
                syntax:'',
                stream: '',
                info_stream: [],

                stream_search_result: '',
                info_streamSR: [],

                //=========================================
                email:'',
                pass:'',
                user_link:'',
                api_key: '',
                site_key:'',
                proxy: '',


            }
        },
        methods:{
            setStream()
            {
                axios({
                    method: 'post',
                    url: '/setStream',
                    data:{
                        stream:  this.stream
                    }
                })
                .then(response=> {
                    this.getStream();
                });
            },
            importExcel()
            {
                if(this.file == null)
                {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Chọn file csv',
                    });

                    return;
                }

                const fd = new FormData();
                fd.append('file',this.file);
                fd.append('type',this.type);
                fd.append('syntax',this.syntax);

                axios.post('/importExcel',fd,{
                    onUploadProgress: () => {
                        Swal.fire({
                            icon: 'info',
                            title: 'Thông báo',
                            text: 'Đang import ...',
                            showConfirmButton: false
                        });
                    }
                })
                .then(function (response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thông báo',
                        text: 'Import file hoàn tất',
                    });
                })
                .catch(error=>{
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Import file lỗi',
                    });
                });
            },
            onChange()
            {
                this.file = document.getElementById('file').files[0];
                console.log(this.file);

            },
            getStream()
            {
                axios.get('/getStream')
                    .then(response=> {
                        this.info_stream = response.data;
                    })
                    .catch(error=>{

                    });

                this.intervalid1 = setInterval(function(){
                    axios.get('/getStream')
                    .then(response=> {
                        this.info_stream = response.data;
                    })
                    .catch(error=>{

                    });
                }.bind(this), 30000);
            },

            //=================================================================================
            setStreamSearchResult()
            {
                axios({
                    method: 'post',
                    url: '/setStreamSR',
                    data:{
                        stream:  this.stream_search_result
                    }
                })
                .then(response=> {
                    this.getStreamSearchResult();
                });
            },
            getStreamSearchResult()
            {
                axios.get('/getStreamSR')
                    .then(response=> {
                        this.info_streamSR = response.data;
                    })
                    .catch(error=>{

                    });

                this.intervalid1 = setInterval(function(){
                    axios.get('/getStreamSR')
                        .then(response=> {
                            this.info_streamSR = response.data;
                        })
                        .catch(error=>{

                        });
                }.bind(this), 30000);
            },

            //=======================================================================================================
            saveGoogle()
            {
                axios({
                    method: 'post',
                    url: '/setAccount',
                    data:{
                        email:  this.email,
                        pass: this.pass,
                        user_link: this.user_link
                    }
                })
                .then(response=> {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thông báo',
                        text: 'Lưu thành công',
                    });
                });
            },
            getAccount()
            {
                axios({
                    method: 'post',
                    url: '/getAccount',
                })
                .then(response=> {
                    this.email = response.data.email;
                    this.pass = response.data.password;
                    this.user_link = response.data.user_link;
                });
            },

            saveCaptcha()
            {
                axios({
                    method: 'post',
                    url: '/setCaptcha',
                    data:{
                        api_key:  this.api_key,
                        site_key: this.site_key
                    }
                })
                    .then(response=> {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thông báo',
                            text: 'Lưu thành công',
                        });
                    });
            },

            getCaptcha()
            {
                axios({
                    method: 'post',
                    url: '/getCaptcha',
                })
                    .then(response=> {
                        this.api_key = response.data.api_key;
                        this.site_key = response.data.site_key;
                    });
            },

            saveProxy()
            {
                axios({
                    method: 'post',
                    url: '/setProxy',
                    data:{
                        proxy:  this.proxy,
                    }
                })
                    .then(response=> {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thông báo',
                            text: 'Lưu thành công',
                        });
                    });
            },
            getProxy()
            {
                axios({
                    method: 'post',
                    url: '/getProxy',

                })
                .then(response=> {

                    this.proxy = response.data;
                });
            },
            //========================================================
            loginGG()
            {
                axios({
                    method: 'post',
                    url: '/loginGG',

                })
                .then(response=> {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thông báo',
                        text: 'Đã lưu cokie vào thư mục ../public/cookie_gg.txt',
                    });
                })
                .catch(error=>{
                    Swal.fire({
                        icon: 'error',
                        title: 'Thông báo',
                        text: 'Lỗi',
                    });
                })
            }
        },
        created()
        {
            this.getStream();
            this.getStreamSearchResult();
            this.getAccount();
            this.getCaptcha();
            this.getProxy();
        }
    }
</script>

<style scoped>

</style>
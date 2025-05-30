<template>
    <div>
        <app-header title="基础配置" :show-back="true" @back="switchComponent('MainConfigPage')" />

        <v-container>
            <!-- 请求内容配置 -->
            <v-card class="mb-6">
                <v-card-title>请求内容配置</v-card-title>
                <v-card-text>
                    <div class="d-flex flex-wrap align-center gap-4">
                        <v-chip class="ma-2" :color="config.fetch_migu ? 'primary' : 'grey'" variant="outlined">
                            <v-checkbox v-model="config.fetch_migu" label="拉取 migu.m3u 内容" density="comfortable"
                                hide-details></v-checkbox>
                        </v-chip>
                        <v-btn density="comfortable" variant="text" class="px-2" @click="showUidDialog = true"
                            :color="config.fetch_migu ? 'primary' : 'grey'" :disabled="!config.fetch_migu">
                            <span class="text-none">咪咕 UID</span>
                            <v-icon end>mdi-pencil</v-icon>
                        </v-btn>
                        <v-btn density="comfortable" variant="text" class="px-2" @click="showTokenDialog = true"
                            :color="config.fetch_migu ? 'primary' : 'grey'" :disabled="!config.fetch_migu">
                            <span class="text-none">咪咕 Token</span>
                            <v-icon end>mdi-pencil</v-icon>
                        </v-btn>
                    </div>
                </v-card-text>
            </v-card>

            <!-- 咪咕 UID 对话框 -->
            <v-dialog v-model="showUidDialog" max-width="500">
                <v-card>
                    <v-card-title>咪咕 UID</v-card-title>
                    <v-card-text>
                        <v-text-field v-model="tempUid" label="咪咕 UID" variant="outlined" placeholder="请输入咪咕 UID"
                            hide-details></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey" variant="text" @click="showUidDialog = false">取消</v-btn>
                        <v-btn color="primary" variant="text" @click="saveUid">确定</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- 咪咕 Token 对话框 -->
            <v-dialog v-model="showTokenDialog" max-width="500">
                <v-card>
                    <v-card-title>咪咕 Token</v-card-title>
                    <v-card-text>
                        <v-text-field v-model="tempToken" label="咪咕 Token" variant="outlined" placeholder="请输入咪咕 Token"
                            hide-details></v-text-field>
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="grey" variant="text" @click="showTokenDialog = false">取消</v-btn>
                        <v-btn color="primary" variant="text" @click="saveToken">确定</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!-- 链接输出配置 -->
            <v-card class="mb-6">
                <v-card-title>链接输出格式配置</v-card-title>
                <v-card-text>
                    <div class="d-flex flex-wrap gap-4">
                        <v-chip class="ma-2" :color="config.link_output_jump ? 'primary' : 'grey'" variant="outlined">
                            <v-checkbox v-model="config.link_output_jump" label="启用跳转" density="comfortable"
                                hide-details></v-checkbox>
                        </v-chip>
                        <v-chip class="ma-2" :color="config.link_output_desc ? 'primary' : 'grey'" variant="outlined">
                            <v-checkbox v-model="config.link_output_desc" label="启用备注" density="comfortable"
                                hide-details></v-checkbox>
                        </v-chip>
                    </div>
                </v-card-text>
            </v-card>

            <!-- 链接类型配置和排序 -->
            <v-card class="mb-6">
                <v-card-title>直播源类型配置</v-card-title>
                <v-card-text>
                    <draggable v-model="linkTypeKeys" item-key="type" class="d-flex flex-wrap gap-2" ghost-class="ghost"
                        @start="drag = true" @end="drag = false">
                        <template #item="{ element: type }">
                            <v-chip class="ma-2" :color="config.link_type[type] ? 'primary' : 'grey'" variant="outlined"
                                draggable>
                                <v-checkbox v-model="config.link_type[type]" :label="type" hide-details
                                    density="compact" class="mr-2"></v-checkbox>
                                <v-icon>mdi-drag</v-icon>
                            </v-chip>
                        </template>
                    </draggable>
                </v-card-text>
            </v-card>

            <!-- 反向代理域名配置 -->
            <v-card class="mb-6">
                <v-card-title>allinone_format 反向代理域名配置</v-card-title>
                <v-card-text>
                    <div class="text-warning mb-2">
                        不使用反向代理请留空！<br>
                        下面这个配置的是 allinone_format 的反向代理域名！<br>
                        不是 allinone 的反向代理域名！<br>
                        格式要求：<br>
                        1、必须是 http 或 https 协议<br>
                        2、不能带 ?xx=xx 参数<br>
                        3、不能以 / 结尾, 如果是 / 结束的请删除最后的 /<br>
                        4、支持带 /path 的反向代理域名, nginx location /path/ 转发到 allinone_format ，同时需要重写路径 rewrite ^/path/(.*)$
                        /$1 break;
                        。<br>
                        比如: https://test.com/path
                    </div>
                    <v-text-field ref="reverseProxyDomainField" v-model="config.reverse_proxy_domain"
                        placeholder="eg: https?://域名[:端口][/path]" variant="outlined" :rules="reverseProxyDomainRules"
                        persistent-hint hint="请在上面填写 allinone_format 反向代理域名">
                    </v-text-field>
                </v-card-text>
            </v-card>

            <!-- 保存按钮 -->
            <v-card>
                <v-card-text class="text-center">
                    <v-btn color="primary" block :loading="saving" @click="save">
                        保存配置
                    </v-btn>
                </v-card-text>
            </v-card>
        </v-container>
    </div>
</template>

<script>
import { computed, ref, watch } from 'vue';
import draggable from 'vuedraggable';
import { updateConfig } from '../api';
import AppHeader from '../components/AppHeader.vue';

export default {
    name: 'BasicConfigPage',
    components: { draggable, AppHeader },
    props: {
        config: {
            type: Object,
            required: true
        }
    },
    emits: ['show-message', 'switch-component'],
    setup(props, { emit }) {
        const saving = ref(false)
        const drag = ref(false)
        const showUidDialog = ref(false)
        const showTokenDialog = ref(false)
        const tempUid = ref('')
        const tempToken = ref('')
        const reverseProxyDomain = ref('');

        const saveUid = () => {
            props.config.migu_uid = tempUid.value
            showUidDialog.value = false
        }

        const saveToken = () => {
            props.config.migu_token = tempToken.value
            showTokenDialog.value = false
        }

        // 打开对话框时初始化临时值
        watch(showUidDialog, (val) => {
            if (val) tempUid.value = props.config.migu_uid
        })

        watch(showTokenDialog, (val) => {
            if (val) tempToken.value = props.config.migu_token
        })

        // 反向代理域名验证规则
        const reverseProxyDomainRules = [
            v => !v || /^https?:\/\//.test(v) || '必须是 http 或 https 协议',
            v => !/\?/.test(v) || '不能带 ?xx=xx 参数',
            v => !/\/$/.test(v) || '不能以 / 结尾'
        ]
        // 验证基础配置
        const validateConfig = () => {
            // 验证是否启用了至少一种直播源类型
            if (!Object.values(props.config.link_type).some(enabled => enabled)) {
                throw new Error('至少需要启用一种直播源类型');
            }

            // 判断 reverseProxyDomainRules 是否通过
            if (props.config.reverse_proxy_domain) { // 只有在 reverse_proxy_domain 不为空时才进行验证
                const reverseProxyErrors = reverseProxyDomainRules
                    .map(rule => rule(props.config.reverse_proxy_domain))
                    .filter(result => result !== true); // 只保留错误消息

                if (reverseProxyErrors.length > 0) {
                    throw new Error('反向代理域名格式错误:\n' + reverseProxyErrors.join('\n')); // 抛出所有错误消息
                }
            }
        }

        // 直播源类型排序
        const linkTypeKeys = computed({
            get: () => Object.keys(props.config.link_type),
            set: (newOrder) => {
                const newLinkType = {}
                newOrder.forEach(key => {
                    newLinkType[key] = props.config.link_type[key]
                })
                props.config.link_type = newLinkType
            }
        })

        const save = async () => {
            try {
                validateConfig()

                saving.value = true
                // 只提交本页面相关的配置
                const configToSave = {
                    fetch_migu: props.config.fetch_migu,
                    migu_uid: props.config.migu_uid,
                    migu_token: props.config.migu_token,
                    link_output_jump: props.config.link_output_jump,
                    link_output_desc: props.config.link_output_desc,
                    link_type: props.config.link_type,
                    reverse_proxy_domain: props.config.reverse_proxy_domain
                }
                await updateConfig(configToSave)
                emit('show-message', '配置保存成功', 'success')
            } catch (error) {
                emit('show-message', error.response?.data?.error || error.message || '保存失败', 'error')
            } finally {
                saving.value = false
            }
        }

        const switchComponent = (componentName) => {
            emit('switch-component', componentName);
        };

        return {
            saving,
            drag,
            linkTypeKeys,
            showUidDialog,
            showTokenDialog,
            tempUid,
            tempToken,
            saveUid,
            saveToken,
            reverseProxyDomainRules,
            save,
            switchComponent
        }
    }
}
</script>
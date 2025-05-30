<?php

namespace Core;

class ChannelManager
{
    private $configManager;
    private $logger;
    private $nameReplacements = [
        // tv.m3u 替换
        'cctv5p' => 'CCTV5+',
        'cgtnen' => 'CGTN',
        'CGTN-记录' => 'CGTN纪录',
        'cgtndoc' => 'CGTN纪录',
        'cgtnru' => 'CGTN俄语',
        'cgtnfr' => 'CGTN法语',
        'cgtnsp' => 'CGTN西语',
        'cgtnar' => 'CGTN阿语',
        '上海东方卫视' => '东方卫视',
        '凤凰卫视中文' => '凤凰卫视中文台',
        '凤凰卫视资讯' => '凤凰卫视资讯台',
        '凤凰卫视香港' => '凤凰卫视香港台',
        'newtv炫舞未来' => 'NewTV炫舞未来',
        '咪咕视频_8M1080_' => '',
        // tptv.m3u 替换
        'cctv5plus' => 'CCTV5+',
        // migu.m3u 替换
        'cctv9documentary' => 'CGTN纪录',
        'cctv俄语' => 'CGTN俄语',
        'cctv法语' => 'CGTN法语',
        'cctv西班牙语' => 'CGTN西语',
        'cctv阿拉伯语' => 'CGTN阿语',

        // 其他
        '_' => '-',
        'cctv' => 'CCTV',
        'cetv' => 'CETV',
        'chc' => 'CHC',
    ];

    // 频道排序规则
    private $channelOrders = [
        'CCTV' => [
            'CCTV4K',
            'CCTV8K',
            'CCTV1',
            'CCTV2',
            'CCTV3',
            'CCTV4',
            'CCTV5',
            'CCTV5+',
            'CCTV6',
            'CCTV7',
            'CCTV8',
            'CCTV9',
            'CCTV10',
            'CCTV11',
            'CCTV12',
            'CCTV13',
            'CCTV14',
            'CCTV15',
            'CCTV16',
            'CCTV17'
        ],
        '卫视' => [
            // 港澳台地区
            "凤凰卫视中文台",
            "凤凰卫视资讯台",
            "凤凰卫视香港台",
            "香港卫视",
            "澳门卫视",
            "台湾卫视",
            // 一线卫视（最受欢迎）
            "湖南卫视",
            "浙江卫视",
            "江苏卫视",
            "东方卫视",
            // 二线卫视（较受欢迎）
            "北京卫视",
            "深圳卫视",
            "广东卫视",
            "山东卫视",
            "天津卫视",
            "东南卫视",
            "湖北卫视",
            "安徽卫视",
            // 三线卫视
            "重庆卫视",
            "黑龙江卫视",
            "辽宁卫视",
            "江西卫视",
            "河北卫视",
            "河南卫视",
            "四川卫视",
            "广西卫视",
            // 其他省级卫视
            "云南卫视",
            "贵州卫视",
            "山西卫视",
            "陕西卫视",
            "吉林卫视",
            "海南卫视",
            "甘肃卫视",
            "内蒙古卫视",
            "宁夏卫视",
            "青海卫视",
            "西藏卫视",
            "新疆卫视",
            "兵团卫视",
            // 其他
            "海峡卫视",
            "南方卫视",
            "大湾区卫视",
            "三沙卫视"
        ],
        '地方' => [
            '北京',      // 北京市
            '上海',      // 上海市
            '上视',      // 上海市
            '湖南',      // 湖南省
            '江苏',      // 江苏省
            '南京',      // 江苏省南京市
            '徐州',      // 江苏省徐州市
            '睢宁',      // 江苏省徐州市睢宁县
            '邳州',      // 江苏省徐州市邳州市
            '常州',      // 江苏省常州市
            '南通',      // 江苏省南通市
            '镇江',      // 江苏省镇江市
            '宿迁',      // 江苏省宿迁市
            '连云港',    // 江苏省连云港市
            '赣榆',      // 江苏省连云港市赣榆区
            '淮安',      // 江苏省淮安市
            '泰州',      // 江苏省泰州市
            '伊春',      // 黑龙江省伊春市
            '临洮'       // 甘肃省定西市临洮县
        ],
        '教育' => [
            '教育',
            '中学生',
            'CETV'
        ],
        '体育' => [
            '赛事',
            '联赛',
            '体育',
            '足球',
            '篮球',
            '网球',
            '高尔夫',
            '赛车',
            '台球'
        ]
    ];

    public function __construct()
    {
        $this->configManager = ConfigManager::getInstance();
        $this->logger = new LogManager();
    }

    public function format($m3uData)
    {
        try {
            // 按原始频道分组格式化
            $originGrouped = $this->formatOriginChannelGroup($m3uData);
            if (!$originGrouped) {
                throw new \Exception('按原始频道分组格式化失败');
            }
            // $this->logger->debug("按原始频道分组格式化完成");
            // $this->logger->debug(json_encode($originGrouped, JSON_UNESCAPED_UNICODE));

            // 按输出频道分组格式化
            $outputGrouped = $this->formatOutputChannelGroup($originGrouped);
            if (!$outputGrouped) {
                throw new \Exception('按输出频道分组格式化失败');
            }
            // $this->logger->debug("按输出频道分组格式化完成");
            // $this->logger->debug(json_encode($outputGrouped, JSON_UNESCAPED_UNICODE));

            return ['origin_grouped' => $originGrouped, 'output_grouped' => $outputGrouped];
        } catch (\Exception $e) {
            $this->logger->error('频道格式化失败: ' . $e->getMessage());
            return false;
        }
    }

    private function formatOriginChannelGroup($m3uData)
    {
        $config = $this->configManager->getConfig();
        $result = [];

        foreach ($m3uData as $item) {
            // link 包含 feiyangdigital/testvideo 的跳过
            if (strpos($item['link'], 'feiyangdigital/testvideo') !== false) {
                // $this->logger->debug("跳过: 包含 feiyangdigital/testvideo 的链接 " . json_encode($item, JSON_UNESCAPED_UNICODE));
                continue;
            }
            // 替换频道名称
            $desc = $this->replaceChannelName($item['desc']);
            // 获取链接类型
            $linkType = $this->getLinkType($item['link']);
            if (!isset($config['link_type'][$linkType]) || !$config['link_type'][$linkType]) {
                $this->logger->debug("跳过: 链接类型 {$linkType} 已禁用 " . json_encode($item, JSON_UNESCAPED_UNICODE));
                continue;
            }

            // 先确定频道分组
            $group = $this->determineChannelGroup($item, $desc);
            if (!$group) {
                $this->logger->debug("跳过: 未找到匹配的原始频道分组 " . json_encode($item, JSON_UNESCAPED_UNICODE));
                continue;
            }

            // 根据分组处理频道信息
            $channelInfo = $this->processChannelInfo($item, $desc, $linkType, $group);
            if (!$channelInfo) {
                $this->logger->debug("跳过: 频道信息处理失败 " . json_encode($item, JSON_UNESCAPED_UNICODE));
                continue;
            }

            // 确保 $result 存在 $group 分组
            if (!isset($result[$group])) {
                $result[$group] = [];
            }

            // 将频道添加到对应分组
            $this->addToGroup($group, $result[$group], $channelInfo);
        }

        return $result;
    }

    private function formatOutputChannelGroup($originGrouped)
    {
        $config = $this->configManager->getConfig();
        $result = [];

        foreach ($config['output_channel_group'] as $outputGroup => $originGroups) {
            $result[$outputGroup] = [];
            foreach ($originGroups as $originGroup) {
                if (isset($originGrouped[$originGroup])) {
                    $result[$outputGroup] = array_merge(
                        $result[$outputGroup],
                        $originGrouped[$originGroup]
                    );
                }
            }
        }

        // 未分配的原始频道分组
        $unassignedOriginGroups = $this->configManager->getUnassignedOriginChannelGroup();
        foreach ($unassignedOriginGroups as $originGroup) {
            if (isset($originGrouped[$originGroup])) {
                $result['其他频道'] = array_merge($result['其他频道'] ?? [], $originGrouped[$originGroup]);
            }
        }

        return $result;
    }

    private function replaceChannelName($name)
    {
        foreach ($this->nameReplacements as $from => $to) {
            $name = str_ireplace($from, $to, $name);
        }
        return $name;
    }

    private function getLinkType($link)
    {
        $config = $this->configManager->getConfig();
        foreach ($config['link_type'] as $linkType => $enabled) {
            // $link 判断 ? 前面的部分是否包含 $linkType
            $link = explode('?', $link)[0];
            if (strpos($link, $linkType) !== false) {
                return $linkType;
            }
        }

        return 'other';
    }

    private function determineChannelGroup($item, $desc)
    {
        // CCTV48K 分组
        // 匹配 CCTV-?[4|8]K
        if (preg_match('/cctv-?([4|8])k/i', $desc)) {
            return 'CCTV48K';
        }
        // 匹配 cctv-?\d\d?-?[4|8]k
        if (preg_match('/cctv-?\d+-?[4|8]k/i', $desc)) {
            return 'CCTV48K';
        }

        // 4K
        if (stripos($desc, '4k') !== false && !preg_match('/cctv/i', $desc)) {
            return '4K';
        }

        // 8K
        if (stripos($desc, '8k') !== false && !preg_match('/cctv/i', $desc)) {
            return '8K';
        }

        // CCTV4欧洲和美洲 分类到 CGTN
        if (strpos($desc, 'CCTV4欧洲') !== false || strpos($desc, 'CCTV4美洲') !== false) {
            return 'CGTN';
        }

        // CCTV
        if (preg_match('/cctv-?([1-9]\d?\+?)/i', $desc)) {
            return 'CCTV';
        }

        // CGTN
        if (preg_match('/cgtn([^-]*)/i', $desc)) {
            return 'CGTN';
        }

        // 央视数字频道
        if ($item['group'] === '央视' || stripos($desc, 'CHC') === 0) {
            return '央视数字';
        }

        // 卫视频道
        // foreach ($this->channelOrders['卫视'] as $ws) {
        //     if (strpos($desc, $ws) !== false) {
        //         return '卫视';
        //     }
        // }
        if (strpos($desc, '卫视') !== false) {
            return '卫视';
        }

        // 数字频道分组
        $digitalChannels = ['求索', 'NewTV', 'iHOT', 'SiTV', '咪视通', '湖南bblive', '易视腾', '熊猫'];
        foreach ($digitalChannels as $dc) {
            if (stripos($desc, $dc) !== false || stripos($item['group'], $dc) !== false) {
                return $dc;
            }
        }

        // 地方频道
        if (preg_match('/^(' . implode('|', $this->channelOrders['地方']) . ')/i', $desc) || preg_match('/^(' . implode('|', $this->channelOrders['地方']) . ')/i', $item['group'])) {
            return '地方';
        }

        // 教育频道
        if (preg_match('/(' . implode('|', $this->channelOrders['教育']) . ')/i', $desc)) {
            return '教育';
        }

        // 体育频道
        if (preg_match('/(' . implode('|', $this->channelOrders['体育']) . ')/i', $desc)) {
            return '体育';
        }

        // 其他频道
        return '其他';
    }

    private function processChannelInfo($item, $desc, $linkType, $group)
    {
        switch ($group) {
            case 'CCTV48K':
                // 匹配 CCTV-?[4|8]K
                if (preg_match('/cctv-?([4|8])k-?(.*)/i', $desc, $matches)) {
                    $channelNum = $matches[1];
                    $extraDesc = isset($matches[2]) ? $matches[2] : '';
                    $channelName = 'CCTV' . $channelNum . 'K';
                    return [
                        'inf' => $item['inf'],
                        'id' => $channelName,
                        'logo' => $item['logo'],
                        'name' => $extraDesc ? $channelName . '-' . $extraDesc : $channelName,
                        'urls' => [[
                            'link' => $item['link'],
                            'type' => $linkType,
                            'desc' => $extraDesc
                        ]]
                    ];
                }
                // 匹配 cctv-?\d\d?-?[4|8]k
                if (preg_match('/cctv-?(\d+)-?([4|8])k-?(.*)/i', $desc, $matches)) {
                    $channelNum = $matches[1];
                    $k = $matches[2];
                    $extraDesc = isset($matches[3]) ? $matches[3] : '';
                    return [
                        'inf' => $item['inf'],
                        'id' => 'CCTV' . $channelNum,
                        'logo' => $item['logo'],
                        'name' => 'CCTV' . $channelNum . '-' . $k . 'K' . ($extraDesc ? '-' . $extraDesc : ''),
                        'urls' => [[
                            'link' => $item['link'],
                            'type' => $linkType,
                            'desc' => $extraDesc
                        ]]
                    ];
                }

            case 'CCTV':
                if (preg_match('/cctv-?([1-9]\d?\+?)/i', $desc, $matches)) {
                    $channelNum = $matches[1];
                    $channelName = 'CCTV' . $channelNum;
                    return [
                        'inf' => $item['inf'],
                        'id' => $channelName,
                        'logo' => $item['logo'],
                        'name' => $channelName,
                        'urls' => [[
                            'link' => $item['link'],
                            'type' => $linkType,
                            'desc' => preg_replace('/cctv-?[1-9]\d?\+?-?/i', '', $desc)
                        ]]
                    ];
                }

            case 'CGTN':
                if (preg_match('/cgtn([^-]*)/i', $desc, $matches)) {
                    $channelName = 'CGTN' . $matches[1];
                    return [
                        'inf' => $item['inf'],
                        'id' => $channelName,
                        'logo' => $item['logo'],
                        'name' => $channelName,
                        'urls' => [[
                            'link' => $item['link'],
                            'type' => $linkType,
                            'desc' => preg_replace('/cgtn[^-]*-?/i', '', $desc)
                        ]]
                    ];
                }

            case '央视数字':
                return [
                    'inf' => $item['inf'],
                    'id' => $desc,
                    'logo' => $item['logo'],
                    'name' => $desc,
                    'urls' => [[
                        'link' => $item['link'],
                        'type' => $linkType,
                        'desc' => ''
                    ]]
                ];

            case '卫视':
                foreach ($this->channelOrders['卫视'] as $ws) {
                    if (stripos($desc, $ws) !== false) {
                        $extraDesc = '';
                        if (preg_match('/-(.*)$/', $desc, $matches)) {
                            $extraDesc = $matches[1];
                        }
                        return [
                            'inf' => $item['inf'],
                            'id' => $ws,
                            'logo' => $item['logo'],
                            'name' => $ws,
                            'urls' => [[
                                'link' => $item['link'],
                                'type' => $linkType,
                                'desc' => $extraDesc
                            ]]
                        ];
                    }
                }

            case '地方':
                $extraDesc = '';
                if (preg_match('/-(.*)$/', $desc, $matches)) {
                    $extraDesc = $matches[1];
                }
                return [
                    'inf' => $item['inf'],
                    'id' => $desc,
                    'logo' => $item['logo'],
                    'name' => $desc,
                    'urls' => [[
                        'link' => $item['link'],
                        'type' => $linkType,
                        'desc' => $extraDesc
                    ]]
                ];

            case '咪咕':
                if (preg_match('/咪咕视频_?-?8M1080_?-?/i', $desc)) {
                    return [
                        'inf' => $item['inf'],
                        'id' => '咪咕4K',
                        'logo' => $item['logo'],
                        'name' => preg_replace('/咪咕视频_?-?8M1080_?-?/i', '', $desc),
                        'urls' => [[
                            'link' => $item['link'],
                            'type' => $linkType,
                            'desc' => ''
                        ]]
                    ];
                } else {
                    return [
                        'inf' => $item['inf'],
                        'id' => $desc,
                        'logo' => $item['logo'],
                        'name' => $desc,
                        'urls' => [[
                            'link' => $item['link'],
                            'type' => $linkType,
                            'desc' => ''
                        ]]
                    ];
                }

            case '求索':
            case 'NewTV':
            case 'iHOT':
            case 'SiTV':
                return [
                    'inf' => $item['inf'],
                    'id' => $desc,
                    'logo' => $item['logo'],
                    'name' => $desc,
                    'urls' => [[
                        'link' => $item['link'],
                        'type' => $linkType,
                        'desc' => ''
                    ]]
                ];

            default:
                return [
                    'inf' => $item['inf'],
                    'id' => $item['id'],
                    'logo' => $item['logo'],
                    'name' => $desc,
                    'urls' => [[
                        'link' => $item['link'],
                        'type' => $linkType,
                        'desc' => ''
                    ]]
                ];
        }
    }

    private function addToGroup($groupName, &$groupMembers, $channelInfo)
    {
        // 确保 $groupMembers 是数组
        if (!is_array($groupMembers)) {
            $groupMembers = [];
        }

        // 查找是否已存在相同频道
        foreach ($groupMembers as $index => $member) {
            if ($member['name'] === $channelInfo['name']) {
                // 查找新URL应该插入的位置
                $insertIndex = count($member['urls']);

                for ($i = 0; $i < count($member['urls']); $i++) {
                    if ($this->compareLinkTypeOrder($channelInfo['urls'][0]['type'], $member['urls'][$i]['type'])) {
                        $insertIndex = $i;
                        break;
                    }
                }

                // 在正确的位置插入新URL
                array_splice($groupMembers[$index]['urls'], $insertIndex, 0, $channelInfo['urls']);
                return;
            }
        }

        // 如果 $groupName in $this->channelOrders，则按预定义顺序排序
        if (isset($this->channelOrders[$groupName])) {
            $insertIndex = $this->findInsertIndexById($groupMembers, $channelInfo, $this->channelOrders[$groupName]);
            array_splice($groupMembers, $insertIndex, 0, [$channelInfo]);
            return;
        }

        // CCTV48K 按照 CCTV 顺序排序
        if (($groupName === 'CCTV48K' || $groupName === 'CCTV') && strpos($channelInfo['id'], 'CCTV') === 0) {
            $insertIndex = $this->findInsertIndexById($groupMembers, $channelInfo, $this->channelOrders['CCTV']);
            array_splice($groupMembers, $insertIndex, 0, [$channelInfo]);
            return;
        }

        if ($groupName === '卫视') {
            $insertIndex = $this->findInsertIndexById($groupMembers, $channelInfo, $this->channelOrders['卫视']);
            array_splice($groupMembers, $insertIndex, 0, [$channelInfo]);
            return;
        }

        if (in_array($groupName, ['地方', '教育'])) {
            $insertIndex = $this->findInsertIndexByName($groupMembers, $channelInfo, $this->channelOrders[$groupName]);
            array_splice($groupMembers, $insertIndex, 0, [$channelInfo]);
            return;
        }

        // 其他频道直接添加到末尾
        $groupMembers[] = $channelInfo;
    }

    private function findInsertIndexById($groupMembers, $channelInfo, $orderArray)
    {
        // 在预定义顺序中查找目标频道的位置
        $targetIndex = array_search($channelInfo['id'], $orderArray);

        // 如果频道不在预定义顺序中，添加到末尾
        if ($targetIndex === false) {
            return count($groupMembers);
        }

        // 遍历现有分组，找到合适的插入位置
        for ($i = 0; $i < count($groupMembers); $i++) {
            $currentId = $groupMembers[$i]['id'];
            $currentIndex = array_search($currentId, $orderArray);

            if ($currentIndex === false) {
                // 当前频道不在预定义顺序中，新频道应该插入在这个位置
                return $i;
            } elseif ($currentIndex > $targetIndex) {
                // 当前频道的顺序在目标频道之后，新频道应该插入在这个位置
                return $i;
            } elseif ($currentIndex === $targetIndex) {
                // ID相同，按 link_type 排序
                if ($this->shouldInsertBeforeSameOrder($channelInfo, $groupMembers[$i])) {
                    return $i;
                }
            }
        }

        // 如果没有找到合适的位置，添加到末尾
        return count($groupMembers);
    }

    private function findInsertIndexByName($groupMembers, $channelInfo, $patterns)
    {
        // 获取目标频道在模式数组中的位置
        $targetIndex = PHP_INT_MAX;
        foreach ($patterns as $index => $pattern) {
            if (stripos($channelInfo['name'], $pattern) !== false) {
                $targetIndex = $index;
                break;
            }
        }

        // 遍历现有分组，找到合适的插入位置
        for ($i = 0; $i < count($groupMembers); $i++) {
            $currentIndex = PHP_INT_MAX;
            foreach ($patterns as $index => $pattern) {
                if (stripos($groupMembers[$i]['name'], $pattern) !== false) {
                    $currentIndex = $index;
                    break;
                }
            }

            if ($currentIndex > $targetIndex) {
                return $i;
            } elseif ($currentIndex === $targetIndex) {
                // 名称匹配相同的模式，按 link_type 排序
                if ($this->shouldInsertBeforeSameOrder($channelInfo, $groupMembers[$i])) {
                    return $i;
                }
            }
        }

        // 如果没有找到合适的位置，添加到末尾
        return count($groupMembers);
    }

    // 比较两个 link_type 的顺序
    private function compareLinkTypeOrder($type1, $type2)
    {

        $linkTypeKeys = $this->configManager->getLinkTypeSortKeys();

        $type1Index = array_search($type1, $linkTypeKeys);
        $type2Index = array_search($type2, $linkTypeKeys);

        return $type1Index < $type2Index;
    }

    // 比较相同顺序的频道，根据 link_type 决定是否应该插入到前面
    private function shouldInsertBeforeSameOrder($newChannel, $existingChannel)
    {
        $currentLinkType = $existingChannel['urls'][0]['type'];
        $newLinkType = $newChannel['urls'][0]['type'];

        return $this->compareLinkTypeOrder($newLinkType, $currentLinkType);
    }
}

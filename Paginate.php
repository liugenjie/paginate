<?php

/**
 * 分页功能
 */
class Cola_Helper_Paginate
{

    /**
     * 页码数据
     * @var Array
     */
    public $pageData;

    function __construct()
    {

    }

    /**
     * 分页
     *
     * @param int $offset url中ofs的值，本页数据条目之前的数据条目
     * @param int $limit 每页显示多少
     * @param int $total 条目总数
     * @param int $distance 间隔，一般情况下的当前页码与最大最小页码的间隔
     * @param string $request_url 当前页面地址
     * @param callback pageHTML  默认 'defaultHTML'，否则必须填写callback
     *
     * 
     * EXAMPLE:
     * $pageHTML = $paginate->page($offset, $limit, $soukeSphinx->course_total, $distance, '', function ($url) {
     *       return array(
     *           'index' => '<li class="pages_prev"><a class="btn btn_gray" href="javascript:void(0);" onclick="$(\'#offset\').val(' . $url['index'] . '); $(\'#form\').submit();" target="_self">首页</a></li>', // 首页
     *           'prev' => '<li class="pages_prev"><a class="btn btn_gray" href="javascript:void(0);" onclick="$(\'#offset\').val(' . $url['prev'] . '); $(\'#form\').submit();" target="_self">上一页</a></li>', // 上一页
     *           'curr' => '<li class="pages_current"><span>' . $url['curr'] . '</span></li>', // 中间部分
     *           'middle' => '<li class="pages_item"><a href="javascript:void(0);" onclick="$(\'#offset\').val(' . $url['middle'] . '); $(\'#form\').submit();" target="_self">' . $url['page'] . '</a></li>', // 中间部分
     *           'next' => '<li class="pages_prev"><a class="btn btn_gray" href="javascript:void(0);" onclick="$(\'#offset\').val(' . $url['next'] . '); $(\'#form\').submit();" target="_self">下一页</a></li>', // 下一页
     *           'end' => '<li class="pages_prev"><a class="btn btn_gray" href="javascript:void(0);" onclick="$(\'#offset\').val(' . $url['end'] . '); $(\'#form\').submit();" target="_self">末页</a></li>' // 末页
     *       );
     *   }); 
     *
     * @return array 分页HTML标签
     *
     * @access public
     */
    public function page($offset, $limit, $total, $distance = 2, $request_url = '', $pageHtml = 'defaultHtml')
    {
        $this->pageData = array();
        $this->pageData ['total'] = $total;
        $this->pageData ['limit'] = $limit;
        $this->pageData ['request_url'] = empty ($request_url) ? $_SERVER ['REQUEST_URI'] : $request_url;
        $this->pageData ['curr_num'] = ceil($offset / $this->pageData ['limit']) + 1; // 当前页码
        $this->pageData ['max_num'] = ceil($total / $this->pageData ['limit']); // 最大页码
        $this->pageData ['start_num'] = ($this->pageData ['curr_num'] - $distance) <= 0 ? 1 : ($this->pageData ['curr_num'] - $distance); // 在最左侧显示的页码
        $this->pageData ['end_num'] = (($this->pageData ['curr_num'] + $distance) < $this->pageData ['max_num']) ? $this->pageData ['curr_num'] + $distance : $this->pageData ['max_num']; // 在最右侧显示的页码
        if ($pageHtml == 'defaultHtml') {
            return $this->{$pageHtml}($this->pageData);
        } elseif (!is_callable($pageHtml)) {
            throw new InvalidArgumentException('Second param must be callback');
        } else {
            return $this->definedHtml($this->pageData, $pageHtml);
        }

    }

    /**
     * 利用数据组装出标签
     *
     * @param int $pageData ['curr_num'] 当前页码
     * @param int $pageData ['start_num'] 开始页码
     * @param int $pageData ['end_num'] 结束页码
     * @param int $pageData ['max_num'] 最大页码
     * @param int $pageData ['limit'] 每页多少个
     * @param string $pageData ['request_url'] 当前页面地址
     *
     * @return array 分页HTML标签
     *
     * @access public
     */
    public function defaultHtml($pageData)
    {
        $pageHTML = '';
        // 首页
        $pageHTML .= '<a href="' . $this->_request_url(0) . '" target="_self">首页</a>';
        // 上一页
        if (1 < $pageData ['curr_num'])
            $pageHTML .= '<a href="' . $this->_request_url($pageData ['curr_num'] - 2) . '" target="_self" style="margin:0px 5px;">上一页</a>';

        // 中间部分
        for ($i = $pageData ['start_num']; $i <= $pageData ['end_num']; $i++) {
            if ($i == $pageData ['curr_num'])
                $pageHTML .= '<a>' . $pageData ['curr_num'] . '</a>';
            else
                $pageHTML .= '<a href="' . $this->_request_url($i - 1) . '" target="_self" style="margin:0px 5px;">' . $i . '</a>';
        }
        // 下一页
        if ($pageData ['curr_num'] < $pageData ['max_num'])
            $pageHTML .= '<a href="' . $this->_request_url($pageData ['curr_num']) . '" target="_self" style="margin:0px 5px;">下一页</a>';

        // 末页
        $pageHTML .= '<a href="' . $this->_request_url($pageData ['max_num'] - 1) . '" target="_self">末页</a>';

        return $pageHTML;
    }

    /**
     * 利用数据组装出标签1
     *
     * @param int $pageData ['curr_num'] 当前页码
     * @param int $pageData ['start_num'] 开始页码
     * @param int $pageData ['end_num'] 结束页码
     * @param int $pageData ['max_num'] 最大页码
     * @param int $pageData ['limit'] 每页多少个
     * @param string $pageData ['request_url'] 当前页面地址
     *
     * @return array 分页HTML标签
     *
     * @access public
     */
    public function definedHtml($pageData, $pageHtml)
    {
        $HTML = '';
        $_a_arr = $pageHtml(array(
            'index' => 0, // 首页
            'prev' => $pageData ['curr_num'] - 2, // 上一页
            'curr' => $pageData ['curr_num'], // 当前页码
            'middle' => 9999999999, // 中间部分
            'page' => 'placeholder_number', // 中间部分的页码
            'next' => $pageData ['curr_num'], // 下一页
            'end' => $pageData ['max_num'] - 1 // 末页
        ));
        $HTML .= $_a_arr['index']; // 首页
        if (1 < $pageData ['curr_num']) $HTML .= $_a_arr['prev']; // 上一页

        // 中间部分
        for ($i = $pageData ['start_num']; $i <= $pageData ['end_num']; $i++) {
            if ($i == $pageData ['curr_num'])
                $HTML .= $_a_arr['curr']; // 中间部分
            else
                $HTML .= preg_replace(array('/placeholder_number/', '/9999999999/'), array($i, ($i - 1) * $pageData ['limit']), $_a_arr['middle']); // 中间部分
        }

        if ($pageData ['curr_num'] < $pageData ['max_num']) $HTML .= $_a_arr['next']; // 下一页
        $HTML .= $_a_arr['end']; // 末页
        return $HTML;
    }

    /**
     * 处理地址
     *
     * @param int $arg 页码
     *
     * @return string 处理好的地址
     *
     * @access public
     */
    private function _request_url($arg)
    {
        $ofs = $arg * $this->pageData ['limit'];
        $parse = parse_url($this->pageData ['request_url']);
        if (strpos($this->pageData ['request_url'], 'ofs') != FALSE) {
            return preg_replace('/ofs=(\d*)?/', 'ofs=' . $ofs, $this->pageData ['request_url']);
        } elseif (empty ($parse ['query'])) {
            return $this->pageData ['request_url'] . '?ofs=' . $ofs;
        } else {
            return $this->pageData ['request_url'] . '&ofs=' . $ofs;
        }
    }

}

?>

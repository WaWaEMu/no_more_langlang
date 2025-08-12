export interface PetInter {
  [petType: string]: string[]
}

// List of pet types and their color options
export const pets: PetInter = {
    "貓咪": ["黑色", "黑色穿襪子", "橘色", "三花", "虎斑", "玳瑁", "賓士", "狸花", "白色", "灰色", "奶油色", "其他"],
    "狗狗": ["黑色", "黑色穿襪子", "白色", "白色穿襪子", "巧克力色", "虎斑", "深黃色", "淡黃色", "灰色", "橘色", "乳牛色", "其他"]
}
export interface FormInter {
    title: string;
    city: string;
    town: string;
    isStray: boolean | null;
    type: string;
    color: string;
    furType: string;
    name: string;
    gender: string;
    age: string;
    isNeuter: boolean | null;
    canSendCity: string[];
    des: string;
    healthDes: string;
    cond: string;
    img: {
        previewList: [string, string, string],
        originalList: [string, string, string]
    };
}
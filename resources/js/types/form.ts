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
    sendableCity: string[];
    description: string;
    healthDescription: string;
    condition: string;
    image: {
        previewList: [string, string, string],
        originalList: [string, string, string]
    };
}